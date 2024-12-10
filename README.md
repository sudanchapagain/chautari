<div align="center">
    <h1>Chautari</h1>
    <p>A simple event booking system built with PHP and PostgresDB.</p>
</div>

> some of the choices made may seem impractical or outright nonsense but it is
  because of assigned project's supervisor. The choices go outside of the course
  work's design too.

## issues

1. on container initialization, `init.sql` is not executed automatically. fix:
   ```console
   docker compose exec db bash
   psql -U postgres -d event_booking_system -f docker-entrypoint-initdb.d/init.sql
   ```
