#!/usr/bin/env node
/**
 * Seed Canvas with users, courses, sections, and enrollments via SIS Import
 * Processes each CSV type sequentially to ensure proper dependency order
 */

import JSZip from "jszip";
import { canvasConfig, accountId, validateConfig } from "./config.js";

// Blueprint Course Scenario:
// - PSY-1001-BLUEPRINT: Blueprint course (template) - created FIRST
// - PSY-1001-001: Child course (Section 001) - ainstructor only
// - PSY-1001-002: Child course (Section 002) - binstructor only
// Non-Blueprint Course:
// - SPAN-1001: Regular course - both ainstructor and binstructor co-teaching

// Step 1: Create blueprint course first
const BLUEPRINT_COURSES_CSV = `course_id,short_name,long_name,status
PSY-1001-BLUEPRINT,PSY 1001 Blueprint,"Introduction to Psychology - Blueprint",active
`;

// Step 2: Create child and regular courses (can reference blueprint now)
const COURSES_CSV = `course_id,short_name,long_name,status,blueprint_course_id
PSY-1001-001-FA25,PSY 1001-001,"Introduction to Psychology - Section 001 (Fall 2025)",active,PSY-1001-BLUEPRINT
PSY-1001-002-FA25,PSY 1001-002,"Introduction to Psychology - Section 002 (Fall 2025)",active,PSY-1001-BLUEPRINT
SPAN-1001-FA25,SPAN 1001,"Spanish I (Fall 2025)",active,
`;

const SECTIONS_CSV = `section_id,course_id,name,status
PSY-1001-BLUEPRINT-SEC,PSY-1001-BLUEPRINT,"Blueprint Section",active
PSY-1001-001-SEC,PSY-1001-001-FA25,"Section 001",active
PSY-1001-002-SEC,PSY-1001-002-FA25,"Section 002",active
SPAN-1001-SEC,SPAN-1001-FA25,"Spanish I",active
`;

// Cleanup: Move old SIS IDs out of the way so we can reuse them
const CLEANUP_TIMESTAMP = Date.now();

// Step 1: Change SIS IDs of old users to free up the IDs
const CHANGE_SIS_ID_CSV = `old_id,new_id,type
4004,DELETED-4004-${CLEANUP_TIMESTAMP},user
4005,DELETED-4005-${CLEANUP_TIMESTAMP},user
4006,DELETED-4006-${CLEANUP_TIMESTAMP},user
`;

// Step 2: Delete the renamed users (optional but clean)
const DELETE_USERS_CSV = `user_id,login_id,status
DELETED-4004-${CLEANUP_TIMESTAMP},DELETED-4004-${CLEANUP_TIMESTAMP},deleted
DELETED-4005-${CLEANUP_TIMESTAMP},DELETED-4005-${CLEANUP_TIMESTAMP},deleted
DELETED-4006-${CLEANUP_TIMESTAMP},DELETED-4006-${CLEANUP_TIMESTAMP},deleted
`;

const USERS_CSV = `user_id,login_id,first_name,last_name,email,status,password
1001,adminuser,Admin,User,latistecharch+adminuser@umn.edu,active,adminuser
2001,ainstructor,Instructor,A,latistecharch+ainstructor@umn.edu,active,ainstructor
2002,binstructor,Instructor,B,latistecharch+binstructor@umn.edu,active,binstructor
4001,astudent,Student,A,latistecharch+astudent@umn.edu,active,astudent
4002,bstudent,Student,B,latistecharch+bstudent@umn.edu,active,bstudent
4003,cstudent,Student,C,latistecharch+cstudent@umn.edu,active,cstudent
4004,dstudent,Student,D,latistecharch+dstudent@umn.edu,active,dstudent
4005,estudent,Student,E,latistecharch+estudent@umn.edu,active,estudent
4006,fstudent,Student,F,latistecharch+fstudent@umn.edu,active,fstudent
`;

// Enrollments:
// Blueprint courses:
// - ainstructor teaches PSY Section 001 only
// - binstructor teaches PSY Section 002 only
// - students A-C enrolled in PSY Section 001
// - students D-F in PSY Section 002
// Non-blueprint course:
// - BOTH ainstructor and binstructor co-teach Spanish
// - Mix of students from both groups in Spanish
const ENROLLMENTS_CSV = `section_id,user_id,role,status
PSY-1001-001-SEC,2001,teacher,active
PSY-1001-002-SEC,2002,teacher,active
PSY-1001-001-SEC,4001,student,active
PSY-1001-001-SEC,4002,student,active
PSY-1001-001-SEC,4003,student,active
PSY-1001-002-SEC,4004,student,active
PSY-1001-002-SEC,4005,student,active
PSY-1001-002-SEC,4006,student,active
SPAN-1001-SEC,2001,teacher,active
SPAN-1001-SEC,2002,teacher,active
SPAN-1001-SEC,4001,student,active
SPAN-1001-SEC,4002,student,active
SPAN-1001-SEC,4003,student,active
SPAN-1001-SEC,4004,student,active
SPAN-1001-SEC,4005,student,active
SPAN-1001-SEC,4006,student,active
`;

const IMPORTS = [
  { name: "change_sis_id.csv", content: CHANGE_SIS_ID_CSV, allowFailure: true },
  { name: "delete_users.csv", content: DELETE_USERS_CSV, allowFailure: true },
  { name: "blueprint_courses.csv", content: BLUEPRINT_COURSES_CSV },
  { name: "courses.csv", content: COURSES_CSV },
  { name: "sections.csv", content: SECTIONS_CSV },
  { name: "users.csv", content: USERS_CSV },
  { name: "enrollments.csv", content: ENROLLMENTS_CSV },
];

const hasImportFailed = (status: any) =>
  ["failed", "failed_with_messages"].includes(status.workflow_state);

const hasImportSucceeded = (status: any) =>
  ["imported", "imported_with_messages"].includes(status.workflow_state);

const apiCall = async (endpoint: string, options: RequestInit = {}) => {
  const response = await fetch(`${canvasConfig.baseUrl}${endpoint}`, {
    ...options,
    headers: {
      Authorization: `Bearer ${canvasConfig.accessToken}`,
      ...options.headers,
    },
  });
  if (!response.ok)
    throw new Error(`API error (${response.status}): ${await response.text()}`);
  return response.json();
};

const enableBlueprintMode = async (sisCourseId: string) => {
  // Get course by SIS ID
  const course = await apiCall(`/api/v1/courses/sis_course_id:${sisCourseId}`);

  // Enable blueprint mode
  const formData = new FormData();
  formData.append("course[blueprint]", "true");

  await apiCall(`/api/v1/courses/${course.id}`, {
    method: "PUT",
    body: formData,
  });

  return course;
};

const publishCourse = async (sisCourseId: string) => {
  // Get course by SIS ID
  const course = await apiCall(`/api/v1/courses/sis_course_id:${sisCourseId}`);

  // Publish the course
  const formData = new FormData();
  formData.append("course[event]", "offer");

  await apiCall(`/api/v1/courses/${course.id}`, {
    method: "PUT",
    body: formData,
  });

  return course;
};

const uploadCsv = async (name: string, content: string) => {
  const zip = new JSZip();
  zip.file(name, content);
  const blob = await zip.generateAsync({ type: "blob" });

  const formData = new FormData();
  formData.append("attachment", blob, "import.zip");

  return apiCall(`/api/v1/accounts/${accountId}/sis_imports`, {
    method: "POST",
    body: formData,
  });
};

const pollImport = async (importId: number, allowFailure = false) => {
  for (let i = 0; i < 30; i++) {
    const status = await apiCall(
      `/api/v1/accounts/${accountId}/sis_imports/${importId}`,
    );

    if (hasImportSucceeded(status)) return status;
    if (allowFailure && hasImportFailed(status)) return status;

    if (hasImportFailed(status)) {
      console.error("\nErrors:", status.processing_errors);
      throw new Error("Import failed");
    }

    process.stdout.write(".");
    await new Promise((r) => setTimeout(r, 1000));
  }
  throw new Error("Import timed out");
};

const main = async () => {
  if (!validateConfig()) process.exit(1);

  console.log("\n🌱 Seeding Canvas...\n");

  for (const { name, content, allowFailure } of IMPORTS) {
    process.stdout.write(`→ ${name}...`);
    const result = await uploadCsv(name, content);

    // Allow cleanup imports to fail gracefully (users might not exist)
    const status = await pollImport(result.id, allowFailure);
    console.log(` ${hasImportSucceeded(status) ? "✓" : "✗"}`);

    // After importing blueprint courses, enable blueprint mode
    if (name === "blueprint_courses.csv") {
      process.stdout.write(`→ Enabling blueprint mode...`);
      await enableBlueprintMode("PSY-1001-BLUEPRINT");
      console.log(` ✓`);
    }
  }

  // Publish all courses
  console.log("");
  const coursesToPublish = [
    "PSY-1001-BLUEPRINT",
    "PSY-1001-001-FA25",
    "PSY-1001-002-FA25",
    "SPAN-1001-FA25",
  ];

  for (const courseId of coursesToPublish) {
    process.stdout.write(`→ Publishing ${courseId}...`);
    await publishCourse(courseId);
    console.log(` ✓`);
  }

  console.log("\n✨ Done! Login with username (password = username)\n");
  console.log("Blueprint Course Scenario:");
  console.log("  📘 PSY 1001 Blueprint (template course)");
  console.log("  📗 PSY 1001-001 (ainstructor + [a-c]student)");
  console.log("  📙 PSY 1001-002 (binstructor + [d-f]student)");
  console.log("");
  console.log("Non-Blueprint Course:");
  console.log("  📕 SPAN 1001 (BOTH instructors + [a-f]student)\n");
};

main().catch((error) => {
  console.error("\n✗", error.message);
  process.exit(1);
});
