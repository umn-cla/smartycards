# Canvas Seeding Scripts

Scripts to seed and reset Canvas with test data for LTI development.

## Setup

1. Generate a Canvas API access token:
   - Log into your Canvas instance (e.g., https://canvas.docker)
   - Go to Account → Settings → Approved Integrations
   - Click "+ New Access Token"
   - Give it a purpose (e.g., "Local Development") and optionally set an expiration
   - Copy the generated token

2. Create a `.env` file in the `scripts/canvas` directory:

```bash
cd scripts/canvas
cp .env.example .env
# Edit .env and add your token
```

The `.env.example` file includes `NODE_TLS_REJECT_UNAUTHORIZED=0` which disables SSL certificate validation for local development with self-signed certificates (mkcert). This is safe for local development but should never be used in production.

## Usage

### Test the connection

First, verify your Canvas connection is working:

```bash
npm run canvas:test
```

This will:
- Verify your configuration
- Test the API connection
- List existing courses in Canvas

### Seed Canvas with test data

If using `.env` file:

```bash
npm run canvas:seed
```

This creates:
- 1 course: MLSP 5211 (001) Fundamentals in Hematology and Hemostasis (Fall 2024)
- 2 sections:
  - 001 UMNTC MLSP 5211 (Fall 2024)
  - 001 UMNTC MLSP 6211 (Fall 2024) [cross-listed]
- 2 instructors (enrolled in both sections)
- 2 TAs (enrolled in both sections)
- 10 students (5 in each section)

**All users are created with password: `password`**

After seeding, the script will display all login IDs. You can log in as any user with their login ID (not email) and password `password`.

### Reset Canvas

```bash
# navigate to `canvas-lms` code folder
cd ../canvas-lms

# Run the Canvas Dev Setup script
# (probably, keep existing config files when prompted but DROP db)
./script/docker_dev_setup.sh

```

## Configuration

You can customize the Canvas instance and account ID using environment variables:

```bash
CANVAS_BASE_URL=https://canvas.docker
CANVAS_ACCESS_TOKEN=your_token_here
CANVAS_ACCOUNT_ID=1
NODE_TLS_REJECT_UNAUTHORIZED=0  # For self-signed certs (dev only)
```

## Troubleshooting

### "SIS ID already in use" errors

We use timestamps in SIS IDs to avoid conflicts. If you encounter this error:
1. Run `npm run canvas:reset` to completely reset the database
2. Re-configure LTI
3. Run `npm run canvas:seed` again

Note: The reset command destroys all Canvas data, so you'll lose your LTI configuration.

### Logging in as seeded users

All seeded users are created with the password `password`. After seeding, the script will display ALL login IDs organized by role.

**To log in**:
1. Go to `https://canvas.docker`
2. Use the **login ID** (e.g., `albert.instructor`, `anna.student`), **not** the email address
3. Password: `password`

**User naming convention**:
- Instructors: Albert Instructor, Betty Instructor
- TAs: Kevin TA, Laura TA
- Students: Patricia Student, Quincy Student, Rachel Student, etc.
- Login IDs: `{firstname}.{role}` (e.g., `albert.instructor`, `kevin.ta`, `patricia.student`)

Each role uses distinct first names for easy identification.

**Note**: `force_self_registration` makes users immediately active without email confirmation, perfect for testing.

**Alternative**: Use Canvas admin masquerade feature:
1. Log in as admin (`canvas@example.com` / `canvas`)
2. Go to Account → Settings → Users
3. Find the user and click "Act as User"
