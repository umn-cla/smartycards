# Canvas Seeding Scripts

Seed Canvas with test data for LTI development via SIS Import.

## Setup

1. Generate Canvas API access token:
   - Log into Canvas (e.g., https://canvas.docker)
   - Account → Settings → Approved Integrations → "+ New Access Token"
   - Copy the token

2. Create `.env` file:

```bash
cd scripts/canvas
cp .env.example .env
# Add your token to .env
```

Note: `.env.example` includes `NODE_TLS_REJECT_UNAUTHORIZED=0` for self-signed certs (dev only).

## Commands

```bash
npm run canvas:test   # Test API connection
npm run canvas:seed   # Seed Canvas with test data
```

## Seeded Data

`npm run canvas:seed` creates:

- **Course**: SPAN 1234 Spanish 1234 -- Sect. 001 (Fall 2025)
- **Sections**:
  - SPAN 1234 001 (Fall 2025)
  - SPAN 2234 001 (Fall 2025)
- **Users**:
  - 1 admin: `adminuser`
  - 2 instructors: `ainstructor`, `binstructor`
  - 2 assistants: `aassistant`, `bassistant`
  - 10 students: `astudent`, `bstudent`, `cstudent`, `dstudent`, `estudent`, `fstudent`, `gstudent`, `hstudent`, `istudent`, `jstudent`

All users are enrolled in both sections. Password matches login_id (e.g., `astudent` / `astudent`, `aassistant` / `aassistant`).

## Reset Canvas

To reset Canvas database:

```bash
cd ../canvas-lms
./script/docker_dev_setup.sh  # Keep config files, DROP database
```

Warning: Destroys all data including LTI configuration.

## Configuration

Environment variables:

```bash
CANVAS_BASE_URL=https://canvas.docker
CANVAS_ACCESS_TOKEN=your_token_here
CANVAS_ACCOUNT_ID=1
NODE_TLS_REJECT_UNAUTHORIZED=0  # Dev only
```
