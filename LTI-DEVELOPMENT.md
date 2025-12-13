# LTI Development with Canvas

Scattered notes on developing locally with Canvas.

## Running Canvas Locally

Notes from following the [Canvas: Developing with Docker guide](https://github.com/instructure/canvas-lms/blob/master/doc/docker/developing_with_docker.md). 

### Clone Canvas Repo

Clone the instructure canvas repo:
```sh
git clone https://github.com/instructure/canvas-lms.git
```

### Run the Docker Setup Script

Within the repo, run 
```sh
./script/docker_dev_setup.sh
```

> [!Important] Troubleshooting
>  On first run, the script complained that there was no`config/database.yml`. 
> I just copied  the `config/database.yml.example` to `config/database.yml` and reran the setup script, which seemed to work.

After setup, check the Docker Dashboard to make sure that the Canvas services are running.

### Set up `canvas.docker` and `smartycards.docker`

Canvas expects its hostname to be `canvas.docker`, and recommends using `dory` or `dingy-http-proxy` to auto-resolve services to service names.

This was fussy for me, so I recommend skipping it and instead we'll do this old school with `nginx`

- Update `/etc/hosts` to forward `canvas.docker` and `smartycards.docker` to localhost
- set up an `nginx` reverse proxy to handle TLS and forward canvas and smartycards traffic to the correct ports
- expose canvas `web` service on `9080`
- expose smartycards `app` service port on `8000`

Details below.

### Expose Canvas `web` service on port `9080`

Edit `docker-compose.override.yml` to expose the `web` service http port of canvas, and (optionally) the postgres db port. (The db port is optional, but helpful if you want to inspect what Canvas is saving internally with the LTI)

In the Canvas LMS codebase:
```yml
# 
# canvas-lms/docker-compose.override.yml (abridged)
services:
  web:
    <<: *BASE
    ports:
      - "9080:80" # expose port 80 on some non-80 port (our nginx reverse proxy will be on 80)
  postgres:
    ports:
      - "5432:5432" # expose postgres
```

Restart canvas:
```sh
docker compose down
docker compose up
```

Verify that you can access Canvas at: <http://127.0.0.1:9080>

## Expose SmartyCards `app` service on port `8000`

We want to direct traffic to the Laravel `app` service (NOT the smartycards' `nginx` service, since we'll be running our own `nginx` on the host).

In `.env` set `APP_PORT` to your desired port. The default is `8000`.

Also, update our `APP_URL` to `smartycards.docker`

```sh
# smartycards/.env
APP_URL=https://smartycards.docker
APP_PORT=8000
```

Verify with <http://127.0.0.1:8000>

### Update `/etc/hosts` 

Add `smartycards.docker` and `canvas.docker` to our `/etc/hosts` file:

```hosts
127.0.0.1    canvas.docker
127.0.0.1    smartycards.docker
```

Verify name resolution with:
- <http://canvas.docker:9080>
- <http://smartycards.docker:8000>

> [!Note]
> It's normal to get a white page at this point with smartycards. When the browser tries to access vite dev proxy server on `https://smartycards.docker:5173` from `http://smartycards.docker:8000`

### Set up reverse nginx proxy on host

If needed, install and start `nginx` on your host. For example, on MacOS:

```sh
# install nginx
brew install nginx
```

Add configuration for `nginx`. If using Homebrew, edit `/opt/homebrew/etc/nginx/nginx.conf`:

```nginx
# /opt/homebrew/etc/nginx/nginx.conf
worker_processes  auto;
error_log  /opt/homebrew/var/log/nginx/error.log;

events {
    worker_connections  1024;
}

http {
  include       mime.types;
  default_type  application/octet-stream;

  ssl_certificate /opt/homebrew/etc/nginx/certs/docker.pem;
  ssl_certificate_key /opt/homebrew/etc/nginx/certs/docker-key.pem;
  ssl_session_cache off;
  ssl_session_tickets off;

  # Proxy settings
  proxy_http_version 1.1;
  proxy_buffering off;
  proxy_request_buffering off;
  proxy_set_header Host $host;
  proxy_set_header X-Real-IP $remote_addr;
  proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
  proxy_set_header X-Forwarded-Proto $scheme;
  proxy_set_header X-Forwarded-Host $host;
  proxy_set_header X-Forwarded-Port $server_port;
  proxy_set_header Upgrade $http_upgrade;
  proxy_set_header Connection "upgrade";
  proxy_read_timeout 300s;
  proxy_connect_timeout 300s;

  client_max_body_size 100M;
  sendfile        on;
  keepalive_timeout  65;
  gzip  on;

  # HTTP -> HTTPS redirect
  server {
      listen 80;
      server_name *.docker;
      return 301 https://$host$request_uri;
  }

  # SmartyCards :8000
  server {
    listen 443 ssl;
    server_name smartycards.docker *.smartycards.docker;
    access_log off;
    error_log  /opt/homebrew/var/log/nginx/smartycards.error.log;
    location / { proxy_pass http://localhost:8000; }
  }

  # Canvas :9080
  server {
    listen 443 ssl;
    server_name canvas.docker *.canvas.docker;
    access_log off;
    error_log  /opt/homebrew/var/log/nginx/canvas.error.log;
    location / { proxy_pass http://localhost:9080; }
  }

  # Catch-all for unmatched domains
  server {
    listen 443 ssl default_server;
    server_name _;
    return 444;
  }

  # include  /opt/homebrew/etc/nginx/sites-enabled/*;
}
```

### Create certs for `*.docker` sites

```sh
# Install mkcert if needed
brew install mkcert
mkcert -install

# Create a wildcard cert for our site
# (note: wildcards only work with one level of subdomains)
mkdir -p /opt/homebrew/etc/nginx/certs
cd /opt/homebrew/etc/nginx/certs
mkcert "*.docker" docker "*.smartycards.docker" smartycards.docker "*.canvas.docker" canvas.docker
mv _wildcard.docker+5.pem /opt/homebrew/etc/nginx/certs/docker.pem
mv _wildcard.docker+5-key.pem /opt/homebrew/etc/nginx/certs/docker-key.pem
```
### Test Nginx config

```sh
nginx -t
# nginx: the configuration file /opt/homebrew/etc/nginx/nginx.conf syntax is ok
# nginx: configuration file /opt/homebrew/etc/nginx/nginx.conf test is successful
```

### Start Nginx

```sh
# start nginx
brew services run nginx
```

Verify sites work as expected:
- <http://canvas.docker> should redirect to <https://canvas.docker>
- <http://smartycards.docker> should redirect to <https://smartycards.docker>

> [!NOTE]
> If your certificate is still showing as unsafe, you may need to restart your browser and/or restart nginx.

## LTI Setup: Canvas

Canvas (at least at the local dev level) has two accounts:
- Site Admin (root)
	- UMN

## Connecting to Canvas' Postgres Database

Be sure the postgres port is expose in `docker-compose.override.yml`:

```yml
  postgres:
    volumes:
      - pg_data:/var/lib/postgresql/data
    ports:
      - "5432:5432"
```

Connect with:

```
PORT: 5432
USER: postgres
PASSWORD: sekret
DATABASE: canvas_development
```

### Resetting the Canvas DB

```
docker compose down
docker volume rm canvas-lms_pg_data
docker compose up --no-start web
docker compose run --rm web bundle exec rake db:create db:initial_setup
docker compose run --rm web bundle exec rake db:migrate RAILS_ENV=test
```

## Setting Up LTI 1.3 Integration

### Canvas Configuration

SmartyCards includes an LTI configuration endpoint at `/lti/config.json` that Canvas can use to auto-populate all settings.

Since local Canvas uses self-signed certificates, the "Paste JSON" method is most reliable:

1. Visit https://smartycards.docker/lti/config.json in your browser and copy the JSON

2. Log into Canvas as Site Admin

3. Go to `Admin > Site Admin > Developer Keys`

4. Click `+ Developer Key > + LTI Key`

5. Select **"Paste JSON"**

6. Paste the JSON from step 1

7. **IMPORTANT: Set Privacy Level**
   - Scroll down to **"LTI Advantage Services"** section
   - Under **"Privacy Level"**, select **"Public"**
   - **Why this is required:** SmartyCards validates that Canvas sends user email and SIS ID during LTI authentication
   - Canvas privacy levels:
     - `Anonymous` - No user info sent (will fail)
     - `Name Only` - Only name sent (will fail - missing email)
     - `Email Only` - Only email sent (will fail - missing SIS ID)
     - `Public` - Full user info including email, name, and SIS ID (required ✓)
   - Without "Public", you'll get validation errors about missing required information

8. Review the auto-filled settings and click **"Save"**

9. **Enable the key** (toggle switch)

10. Copy the **Client ID** (you'll need this later)

11. Go to `Admin > UMN > Settings > Apps`

12. Click `+ App`

13. For Configuration Type, select **"By Client ID"**

14. Enter the Client ID from step 10

15. Save

16. Click the cog (⚙️ settings icon) next to the app, choose **"Deployment Id"**

17. Copy the **Deployment ID** (you'll need this for SmartyCards configuration)

### SmartyCards Configuration

Using the `Client ID` and `Deployment ID` from Canvas, configure SmartyCards via Laravel Nova:

1. Go to `https://smartycards.docker/admin` and log in

2. **Create an LTI Platform:**
   - Click on **"Lti Platforms"** in the sidebar
   - Click **"Create Lti Platform"**
   - Fill in the Canvas platform details:
     - **Name:** `Canvas Local Dev` (or any descriptive name)
     - **Issuer:** `https://canvas.instructure.com` (always, regardless of Canvas host url)
     - **Auth Login URL:** `https://canvas.docker/api/lti/authorize_redirect`
     - **Auth Token URL:** `https://canvas.docker/login/oauth2/token`
     - **Key Set URL:** `https://canvas.docker/api/lti/security/jwks`
   - Click **"Create Lti Platform"**

3. **Create an LTI Deployment:**
   - Click on **"Lti Deployments"** in the sidebar
   - Click **"Create Lti Deployment"**
   - Fill in:
     - **Platform:** Select the platform you just created
     - **Deployment ID:** Paste the Deployment ID from Canvas (step 15 of Canvas Configuration)
     - **Client ID:** Paste the Client ID from Canvas (step 8 of Canvas Configuration)
   - Click **"Create Lti Deployment"**

4. **Test the Integration:**
   - In Canvas, go to your test course
   - Add a new assignment or module item
   - Choose "External Tool"
   - Select SmartyCards from the list
   - Configure the assignment and save
   - Launch the tool to verify the LTI connection works

The Nova admin interface will automatically track:
- **LTI Resource Links** - Created automatically when instructors add SmartyCards to their Canvas course
- **LTI Grade Submissions** - Logged when grades are sent back to Canvas via Assignment and Grade Services (AGS)


## Seeding Canvas with Test Data

We have scripts to seed Canvas with realistic test data for LTI development. See `scripts/canvas/README.md` for full details.

### Quick Start

1. Generate a Canvas API access token:
   - Log into Canvas at <https://canvas.docker>
   - Go to Account → Settings → Approved Integrations
   - Click "+ New Access Token"
   - Give it a purpose (e.g., "Local Development")
   - Copy the generated token

2. Seed Canvas with test data:

```bash
CANVAS_ACCESS_TOKEN=your_token npm run canvas:seed
```

This creates:
- 1 course: MLSP 5211 (001) Fundamentals in Hematology and Hemostasis (Fall 2024)
- 2 sections (including a cross-listed section)
- 2 instructors
- 2 TAs
- 10 students (5 per section)

3. When done testing, reset Canvas:

```bash
CANVAS_ACCESS_TOKEN=your_token npm run canvas:reset
```

### Scripts Location

All Canvas seeding scripts are in `scripts/canvas/`:
- `seed.ts` - Seed Canvas with test data
- `reset.ts` - Remove seeded data
- `lib/canvas-api.ts` - Canvas API utilities
- `lib/data.ts` - Data generation with UMN naming conventions
- `config.ts` - Configuration management

## Troubleshooting

### "Session expired" or Validation Errors During Deep Linking

**Symptoms:**
- When trying to add an assignment external tool in Canvas, you see a "Session expired" error
- Canvas shows its own dashboard instead of the SmartyCards deep link page
- Logs show validation errors about missing `email` or `lis.person_sourcedid` fields

**Root Cause:**
Canvas users created via the API need to have email addresses and LIS (Learning Information Services) data for LTI authentication to work properly. SmartyCards validates these fields when authenticating users from LTI launches.

**Solution:**

1. **Check if Canvas users have emails:**
   - Connect to Canvas's Postgres database (see "Connecting to Canvas' Postgres Database" above)
   - Query to see if users have communication channels:
     ```sql
     SELECT u.id, u.name, cc.path as email
     FROM users u
     LEFT JOIN communication_channels cc ON u.id = cc.user_id AND cc.path_type = 'email'
     WHERE u.workflow_state = 'available'
     ORDER BY u.id;
     ```

2. **If users are missing emails, re-seed Canvas:**
   ```bash
   # Reset Canvas and remove all test data
   CANVAS_ACCESS_TOKEN=your_token npm run canvas:reset

   # Re-seed with users that have proper email addresses
   CANVAS_ACCESS_TOKEN=your_token npm run canvas:seed
   ```

3. **Verify Canvas Developer Key privacy settings:**
   - In Canvas Admin → Developer Keys
   - Edit your SmartyCards LTI key
   - Under "LTI Advantage Services" → "Privacy Level", ensure it's set to **"Public"**
   - See the Canvas Configuration section above for why this is required
   - Any other privacy level will cause validation errors

**Prevention:**
The Canvas seeding scripts now include `communication_channel` data when creating users, so this issue shouldn't occur with newly seeded users.

### Deep Link Page Not Loading in Canvas

**Symptoms:**
- Canvas shows a blank page or its own dashboard when trying to configure a SmartyCards assignment
- Browser console shows errors about blocked third-party cookies

**Solution:**

Check your session cookie settings in `.env`:

```bash
SESSION_SAME_SITE=none
SESSION_SECURE_COOKIE=true
SESSION_PARTITIONED_COOKIE=true
```

These settings are required for LTI to work in Canvas iframes:
- `SESSION_SAME_SITE=none` - Allows cookies to work in cross-site contexts (Canvas → SmartyCards)
- `SESSION_SECURE_COOKIE=true` - Required when SameSite=none (cookies must be sent over HTTPS)
- `SESSION_PARTITIONED_COOKIE=true` - Better privacy in modern browsers for cross-site contexts

### Checking LTI Errors

**Enable Debug Mode:**
In `.env`, ensure:
```bash
APP_DEBUG=true
```

With debug enabled, LTI errors will be displayed with full stack traces instead of generic error messages.

**Check Application Logs:**
```bash
tail -f storage/logs/laravel.log
```

All LTI errors are logged with the tag "LTI Error" including:
- Exception class name
- Error message
- Stack trace

**Use Laravel Telescope:**
Visit `https://smartycards.docker/telescope/requests` to see all incoming LTI requests and their responses in real-time.

### Canvas Configuration Issues

**Verify Configuration Endpoint:**
Visit `https://smartycards.docker/lti/config.json` to ensure the configuration is correct. All placements should use:
- `target_link_uri`: `https://smartycards.docker/lti/launch` (NOT `/lti/deep-link`)
- Deep linking placements should have `message_type`: `LtiDeepLinkingRequest`

**Common Canvas Setup Mistakes:**
- Using wrong Issuer (must be `https://canvas.instructure.com` even for local Canvas)
- Not copying the Deployment ID correctly from Canvas
- Having the Developer Key disabled in Canvas
- Not installing the app at the account level (Admin → UMN → Settings → Apps)
