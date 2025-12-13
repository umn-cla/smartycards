#!/usr/bin/env node
/**
 * Seed Canvas with users, courses, sections, and enrollments via SIS Import
 * Processes each CSV type sequentially to ensure proper dependency order
 */

import JSZip from "jszip";
import { canvasConfig, accountId, validateConfig } from "./config.js";

const COURSES_CSV = `course_id,short_name,long_name,status
SPAN-1234-001-FA25,SPAN-1234-001-FA25,"SPAN 1234 Spanish 1234 -- Sect. 001 (Fall 2025)",active
`;

const SECTIONS_CSV = `section_id,course_id,name,status
SPAN-1234-001-FA25,SPAN-1234-001-FA25,"SPAN 1234 001 (Fall 2025)",active
SPAN-2234-001-FA25,SPAN-1234-001-FA25,"SPAN 2234 001 (Fall 2025)",active
`;

const USERS_CSV = `user_id,login_id,first_name,last_name,email,status
1001,admin,Admin,User,latistecharch+admin@umn.edu,active
2001,ainstructor,Albert,Instructor,latistecharch+ainstructor@umn.edu,active
2002,binstructor,Betty,Instructor,latistecharch+binstructor@umn.edu,active
3001,ata,Alice,TA,latistecharch+ata@umn.edu,active
3002,bta,Bob,TA,latistecharch+bta@umn.edu,active
4001,astudent,Amy,Student,latistecharch+astudent@umn.edu,active
4002,bstudent,Ben,Student,latistecharch+bstudent@umn.edu,active
4003,cstudent,Claire,Student,latistecharch+cstudent@umn.edu,active
4004,dstudent,Dan,Student,latistecharch+dstudent@umn.edu,active
4005,estudent,Emma,Student,latistecharch+estudent@umn.edu,active
4006,fstudent,Frank,Student,latistecharch+fstudent@umn.edu,active
4007,gstudent,Grace,Student,latistecharch+gstudent@umn.edu,active
4008,hstudent,Henry,Student,latistecharch+hstudent@umn.edu,active
4009,istudent,Iris,Student,latistecharch+istudent@umn.edu,active
4010,jstudent,Jack,Student,latistecharch+jstudent@umn.edu,active
`;

const ENROLLMENTS_CSV = `section_id,user_id,role,status
SPAN-1234-001-FA25,2001,teacher,active
SPAN-2234-001-FA25,2001,teacher,active
SPAN-1234-001-FA25,2002,teacher,active
SPAN-2234-001-FA25,2002,teacher,active
SPAN-1234-001-FA25,3001,ta,active
SPAN-2234-001-FA25,3001,ta,active
SPAN-1234-001-FA25,3002,ta,active
SPAN-2234-001-FA25,3002,ta,active
SPAN-1234-001-FA25,4001,student,active
SPAN-1234-001-FA25,4002,student,active
SPAN-1234-001-FA25,4003,student,active
SPAN-1234-001-FA25,4004,student,active
SPAN-1234-001-FA25,4005,student,active
SPAN-2234-001-FA25,4006,student,active
SPAN-2234-001-FA25,4007,student,active
SPAN-2234-001-FA25,4008,student,active
SPAN-2234-001-FA25,4009,student,active
SPAN-2234-001-FA25,4010,student,active
`;

const IMPORTS = [
  { name: "courses.csv", content: COURSES_CSV },
  { name: "sections.csv", content: SECTIONS_CSV },
  { name: "users.csv", content: USERS_CSV },
  { name: "enrollments.csv", content: ENROLLMENTS_CSV },
];

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

const pollImport = async (importId: number) => {
  for (let i = 0; i < 60; i++) {
    const status = await apiCall(
      `/api/v1/accounts/${accountId}/sis_imports/${importId}`,
    );

    if (status.workflow_state === "imported") return status;

    if (
      status.workflow_state === "failed" ||
      status.workflow_state === "failed_with_messages"
    ) {
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

  for (const { name, content } of IMPORTS) {
    process.stdout.write(`→ ${name}...`);
    const result = await uploadCsv(name, content);
    await pollImport(result.id);
    console.log(` ✓`);
  }

  console.log('\n✨ Done! Login with username (password = "password")\n');
};

main().catch((error) => {
  console.error("\n✗", error.message);
  process.exit(1);
});
