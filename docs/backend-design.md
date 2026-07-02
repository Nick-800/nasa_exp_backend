# NASA Station - Laravel Backend Design Specification

## 1. Overview
A monolithic Laravel backend to serve as the API and caching layer for the NASA Station Flutter app. It provides user authentication, NASA API proxying/caching, user profiles, journaling, favoriting, and daily APOD push notifications.

## 2. Architecture & Design Patterns
- **Stack:** Laravel 11/13, PHP 8.2+
- **Database:** MySQL / PostgreSQL
- **Cache/Queue:** Redis (Queue managed via **Laravel Horizon**)
- **Authentication:** Laravel Sanctum (token-based) + Laravel Socialite (Google OAuth)
- **Push Notifications:** Firebase Cloud Messaging (FCM)
- **Core Patterns:**
  - **Thin Controllers & Actions:** Business logic is isolated into single-purpose Action classes (e.g., `CreateJournalEntryAction`, `ToggleFavoriteAction`), keeping controllers strictly for HTTP routing.
  - **API Resources:** All JSON responses will be formatted using `Illuminate\Http\Resources\Json\JsonResource` (e.g., `JournalResource`, `UserResource`) to guarantee consistent and versionable output.
  - **Form Requests & DTOs:** Request validation is strictly handled by `FormRequest` classes, which transform validated data into Data Transfer Objects (DTOs) for Actions.
  - **Route Model Binding:** Implicit and scoped bindings will be used to automatically resolve Eloquent models and prevent unauthorized cross-tenant access.

## 3. Database Schema

All primary entities will utilize PHP 8.2+ typed properties, strict types, and Eloquent `SoftDeletes` where applicable to prevent accidental data loss.

### `users`
- `id`
- `name`
- `email`
- `password`
- `google_id` (nullable, indexed)
- `avatar_url` (nullable)
- `bio` (text, nullable)
- `fcm_token` (string, nullable)
- `notification_time` (time, default '09:00:00')
- `timestamps`
- `deleted_at` (SoftDeletes)

### `favorites`
- `id`
- `user_id` (foreign key, cascading)
- `type` (string/backed enum, e.g., `FavoriteType::APOD`)
- `external_id` (string, e.g., '2026-07-01')
- `metadata` (JSON payload with title/URL to avoid N+1 network calls to NASA)
- `timestamps`

### `journals`
- `id`
- `user_id` (foreign key, cascading)
- `title`
- `content` (text)
- `is_public` (boolean, default false)
- `timestamps`
- `deleted_at` (SoftDeletes)

## 4. API Endpoints

### Authentication
- `POST /api/auth/register`
- `POST /api/auth/login`
- `GET /api/auth/google/url`
- `POST /api/auth/google/callback`
- `POST /api/auth/logout`

### User Management
- `GET /api/user/profile` -> Returns `UserResource`
- `PUT /api/user/profile`
- `PUT /api/user/fcm-token`

### Social & Content (Scoped Bindings Enforced)
- `GET /api/journals` -> Returns `JournalResource::collection`
- `POST /api/journals`
- `GET /api/journals/public`
- `GET /api/favorites`
- `POST /api/favorites`
- `DELETE /api/favorites/{favorite}` (Uses implicit route-model binding)

### NASA Proxies (Cached via Redis)
- `GET /api/nasa/apod`
- `GET /api/nasa/eonet`
- `GET /api/nasa/neo`
- `GET /api/nasa/space-weather`

## 5. Background Jobs & Queues (Horizon)
- **Queue Configuration:** Redis-backed queues managed and monitored by Laravel Horizon.
- **Daily APOD Fetch Job:** 
  - Scheduled daily. Fetches from NASA and caches in Redis.
  - Implements `$tries = 3` and `$backoff = 60` (Exponential Backoff) to handle NASA API rate limiting or downtime gracefully.
- **FCM Push Notification Dispatcher:** 
  - Scheduled command checking for users where `notification_time` matches the current hour.
  - Chunks users and dispatches idempotent queue jobs (`SendApodPushNotification`) to push FCM messages asynchronously.
  - Never swallows exceptions silently; logs to standard error tracker.

## 6. Security & Performance
- **API Key Protection:** NASA API key strictly resides in `.env`.
- **Eager Loading:** Strict adherence to Eloquent eager loading (using `with()`) to eliminate N+1 query problems (e.g., loading `Journal::with('author')`).
- **Caching Strategy:**
  - Aggressive caching of NASA responses (APOD: 24h TTL, EONET/NEO: 15m TTL).
  - Cache invalidation on model events where applicable.

## 7. Testing Strategy
- **Framework:** Pest PHP.
- **Coverage Goal:** >85% strict requirement.
- **Approach:**
  - **Feature Tests:** HTTP endpoints tested via `$this->actingAs($user)` to guarantee authorization rules.
  - **Mocking:** NASA external APIs mocked using `Http::fake()` to ensure tests run fast and without internet dependency.
  - **Queues:** Use `Queue::fake()` to assert FCM and APOD fetch jobs are dispatched correctly without real execution.
