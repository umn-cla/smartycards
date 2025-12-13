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
