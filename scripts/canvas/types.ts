/**
 * Type definitions for Canvas API
 */

export interface CanvasConfig {
  baseUrl: string
  accessToken: string
}

export interface Course {
  id?: number
  name: string
  course_code: string
  sis_course_id: string
  term_id?: number
}

export interface Section {
  id?: number
  name: string
  sis_section_id: string
  course_id?: number
}

export interface User {
  id?: number
  name: string
  short_name: string
  sortable_name: string
  sis_user_id: string
  email?: string
  login_id: string
}

export interface Enrollment {
  user_id: number
  type: 'StudentEnrollment' | 'TeacherEnrollment' | 'TaEnrollment'
  enrollment_state: 'active' | 'invited' | 'inactive'
}

export interface FixtureUser {
  username: string
  umndid: string
  emplid: string
  first_name: string
  last_name: string
  email: string
  role: 'admin' | 'instructor' | 'ta' | 'student'
}

export interface Fixtures {
  users: FixtureUser[]
}
