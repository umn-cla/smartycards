function env(key: string, defaultValue: string): string {
  return import.meta.env[key] || defaultValue;
}

export default {
  api: {
    origin: env("VITE_API_ORIGIN", "http://localhost:8000"),
    baseUrl: env("VITE_API_BASE_URL", "http://localhost:8000/api"),
    loginUrl: env("VITE_LOGIN_URL", "http://localhost:8000/login"),
    logoutUrl: env("VITE_LOGOUT_URL", "http://localhost:8000/logout"),
    adminUrl: env("VITE_ADMIN_URL", "http://localhost:8000/admin"),
  },
  client: {
    baseUrl: env("VITE_CLIENT_BASE_URL", "http://localhost:5173"),
  },
};
