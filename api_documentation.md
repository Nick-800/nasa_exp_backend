# NASA Explorer - API Documentation & Integration Guide

This document provides the complete API specification for the **NASA Explorer** backend. It is designed to help you quickly connect and integrate the backend services with your **Flutter mobile application**.

---

## 1. Connection & Environment Setup (Flutter)

### Base URL Configuration
When connecting a mobile app or emulator to a local backend, you cannot use `localhost` or `127.0.0.1` because the emulator runs in its own sandboxed network environment.

- **Android Emulator:** Use `http://10.0.2.2:8000/api` (assuming your Laravel dev server runs on port 8000).
- **iOS Simulator:** Use `http://localhost:8000/api` or `http://127.0.0.1:8000/api`.
- **Physical Device:** Use the local IP address of your development machine (e.g., `http://192.168.1.50:8000/api`). Ensure your device and machine are connected to the same Wi-Fi network.

### Required Headers
All requests must include the following headers:
```http
Accept: application/json
Content-Type: application/json
```
For authenticated endpoints, you must also include the Sanctum Bearer token:
```http
Authorization: Bearer <your_access_token>
```

---

## 2. Authentication Endpoints

### 2.1 Register User
Register a new user account and obtain an access token.

- **Endpoint:** `POST /api/auth/register`
- **Authentication:** None (Public)
- **Request Body:**
```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "securepassword123"
}
```
- **Success Response (201 Created):**
```json
{
  "access_token": "1|LaravelSanctumTokenPlaintextString...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Jane Doe",
    "email": "jane@example.com",
    "avatar_url": null,
    "bio": null,
    "notification_time": null,
    "created_at": "2026-07-09T21:51:56+02:00"
  }
}
```

---

### 2.2 Login User
Authenticate using an email and password.

- **Endpoint:** `POST /api/auth/login`
- **Authentication:** None (Public)
- **Request Body:**
```json
{
  "email": "jane@example.com",
  "password": "securepassword123"
}
```
- **Success Response (200 OK):**
```json
{
  "access_token": "2|LaravelSanctumTokenPlaintextString...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Jane Doe",
    "email": "jane@example.com",
    "avatar_url": null,
    "bio": null,
    "notification_time": null,
    "created_at": "2026-07-09T21:51:56+02:00"
  }
}
```

---

### 2.3 Google OAuth URL
Get the redirection URL for starting the Google OAuth 2.0 flow.

- **Endpoint:** `GET /api/auth/google/url`
- **Authentication:** None (Public)
- **Success Response (200 OK):**
```json
{
  "url": "https://accounts.google.com/o/oauth2/v2/auth?client_id=..."
}
```

---

### 2.4 Google OAuth Callback
Submit the Google callback verification parameters to authenticate.

- **Endpoint:** `POST /api/auth/google/callback`
- **Authentication:** None (Public)
- **Success Response (200 OK):**
```json
{
  "access_token": "3|GoogleAuthSanctumTokenPlaintextString...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Jane Doe",
    "email": "jane@gmail.com",
    "avatar_url": "https://lh3.googleusercontent.com/a/...",
    "bio": null,
    "notification_time": null,
    "created_at": "2026-07-09T21:51:56+02:00"
  }
}
```

---

### 2.5 Logout
Revoke the current authentication token.

- **Endpoint:** `POST /api/auth/logout`
- **Authentication:** Required (Bearer token)
- **Success Response (200 OK):**
```json
{
  "message": "Logged out successfully"
}
```

---

## 3. User Profile Endpoints

### 3.1 Get Profile
Retrieve details of the currently authenticated user.

- **Endpoint:** `GET /api/user/profile`
- **Authentication:** Required (Bearer token)
- **Success Response (200 OK):**
```json
{
  "id": 1,
  "name": "Jane Doe",
  "email": "jane@example.com",
  "avatar_url": "https://example.com/avatar.jpg",
  "bio": "Avid space enthusiast.",
  "notification_time": "22:00:00",
  "created_at": "2026-07-09T21:51:56+02:00"
}
```

---

### 3.2 Update Profile
Update the user's profile information.

- **Endpoint:** `PUT /api/user/profile`
- **Authentication:** Required (Bearer token)
- **Request Body (All fields optional):**
```json
{
  "name": "Jane the Astronaut",
  "bio": "Living life in orbit.",
  "avatar_url": "https://example.com/avatar_new.jpg",
  "notification_time": "08:30:00",
  "fcm_token": "fcm-registration-token-example"
}
```

- **Success Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "name": "Jane the Astronaut",
    "email": "jane@example.com",
    "avatar_url": "https://example.com/avatar_new.jpg",
    "bio": "Living life in orbit.",
    "notification_time": "08:30:00",
    "created_at": "2026-07-09T21:51:56+02:00"
  }
}
```

---

### 3.3 Update FCM Token
Specifically update the Firebase Cloud Messaging device token.

- **Endpoint:** `PUT /api/user/fcm-token`
- **Authentication:** Required (Bearer token)
- **Request Body:**
```json
{
  "fcm_token": "fcm-registration-token-example-12345"
}
```
- **Success Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "name": "Jane the Astronaut",
    "email": "jane@example.com",
    "avatar_url": "https://example.com/avatar_new.jpg",
    "bio": "Living life in orbit.",
    "notification_time": "08:30:00",
    "created_at": "2026-07-09T21:51:56+02:00"
  }
}
```

---

## 4. Space Journals Endpoints

### 4.1 Get Personal Journals
Retrieve all journal entries created by the authenticated user, paginated.

- **Endpoint:** `GET /api/journals`
- **Query Parameters:**
  - `page` (optional): Integer (default is 1).
- **Authentication:** Required (Bearer token)
- **Success Response (200 OK):**
```json
{
  "data": [
    {
      "id": 5,
      "title": "First Night Observing Saturn",
      "content": "Saw the rings clearly through an 8-inch telescope tonight. Breathtaking!",
      "is_public": true,
      "created_at": "2026-07-09T19:00:00Z",
      "updated_at": "2026-07-09T19:00:00Z"
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/journals?page=1",
    "last": "http://localhost:8000/api/journals?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://localhost:8000/api/journals",
    "per_page": 20,
    "to": 1,
    "total": 1
  }
}
```

---

### 4.2 Create Journal Entry
Create a new journal entry.

- **Endpoint:** `POST /api/journals`
- **Authentication:** Required (Bearer token)
- **Request Body:**
```json
{
  "title": "Stargazing at Yosemite",
  "content": "The Milky Way was visible to the naked eye. Observed various constellations.",
  "is_public": true
}
```
- **Success Response (201 Created):**
```json
{
  "data": {
    "id": 6,
    "title": "Stargazing at Yosemite",
    "content": "The Milky Way was visible to the naked eye. Observed various constellations.",
    "is_public": true,
    "created_at": "2026-07-09T21:52:00Z",
    "updated_at": "2026-07-09T21:52:00Z"
  }
}
```

---

### 4.3 Public Feed
Fetch public journals shared by other users.

- **Endpoint:** `GET /api/journals/public`
- **Authentication:** Required (Bearer token)
- **Success Response (200 OK):**
```json
{
  "data": [
    {
      "id": 5,
      "title": "First Night Observing Saturn",
      "content": "Saw the rings clearly through an 8-inch telescope tonight. Breathtaking!",
      "is_public": true,
      "author": {
        "id": 2,
        "name": "John Stargazer",
        "email": "john@example.com",
        "avatar_url": "https://example.com/john.jpg",
        "bio": null,
        "notification_time": null,
        "created_at": "2026-07-09T21:51:56+02:00"
      },
      "created_at": "2026-07-09T19:00:00Z",
      "updated_at": "2026-07-09T19:00:00Z"
    }
  ]
}
```

---

## 5. Favorites Endpoints

Allows users to bookmark NASA APOD photos, EONET events, NEO objects, or Trek features.

### 5.1 List Favorites
Retrieve all bookmarks saved by the authenticated user.

- **Endpoint:** `GET /api/favorites`
- **Authentication:** Required (Bearer token)
- **Success Response (200 OK):**
```json
{
  "data": [
    {
      "id": 10,
      "type": "apod",
      "external_id": "2026-07-01",
      "metadata": {
        "title": "Cool APOD Photo of Aurora",
        "url": "https://apod.nasa.gov/apod/image/2607/aurora_example.jpg"
      },
      "created_at": "2026-07-09T21:51:56Z",
      "updated_at": "2026-07-09T21:51:56Z"
    }
  ]
}
```

---

### 5.2 Add to Favorites
Add a new item to favorites.

- **Endpoint:** `POST /api/favorites`
- **Authentication:** Required (Bearer token)
- **Request Body:**
```json
{
  "type": "apod",
  "external_id": "2026-07-01",
  "metadata": {
    "title": "Cool APOD Photo of Aurora",
    "url": "https://apod.nasa.gov/apod/image/2607/aurora_example.jpg"
  }
}
```
- **Validation Rules:**
  - `type`: Required, string. Standard enum values: `apod`, `neo`, `eonet`, `trek`, `space_weather`.
  - `external_id`: Required, string.
  - `metadata`: Optional, array/object containing display data.

- **Success Response (201 Created):**
```json
{
  "data": {
    "id": 11,
    "type": "apod",
    "external_id": "2026-07-01",
    "metadata": {
      "title": "Cool APOD Photo of Aurora",
      "url": "https://apod.nasa.gov/apod/image/2607/aurora_example.jpg"
    },
    "created_at": "2026-07-09T21:52:00Z",
    "updated_at": "2026-07-09T21:52:00Z"
  }
}
```

---

### 5.3 Remove Favorite
Remove a bookmarked item by its database Favorite ID.

- **Endpoint:** `DELETE /api/favorites/{favorite_id}`
- **Authentication:** Required (Bearer token)
- **Success Response:** `204 No Content` (Empty body).

---

## 6. NASA Proxy Endpoints

### 6.1 APOD (Astronomy Picture of the Day)
- **Endpoint:** `GET /api/nasa/apod`
- **Query Parameters:**
  - `date` (optional): Date formatted as `Y-m-d` (e.g., `2026-07-01`).
- **Success Response (200 OK):**
```json
{
  "date": "2026-07-01",
  "explanation": "This beautiful aurora borealis...",
  "hdurl": "https://apod.nasa.gov/apod/image/2607/aurora_hd.jpg",
  "media_type": "image",
  "title": "Aurora Over Mountain Ridge",
  "url": "https://apod.nasa.gov/apod/image/2607/aurora_standard.jpg"
}
```

---

### 6.2 EONET (Earth Observatory Natural Event Tracker)
- **Endpoint:** `GET /api/nasa/eonet`
- **Success Response (200 OK):**
```json
{
  "events": [
    {
      "id": "EONET_10254",
      "title": "Wildfire - Western Montana",
      "categories": [{"id": "wildfires", "title": "Wildfires"}],
      "geometry": [{"date": "2026-07-02T18:00:00Z", "type": "Point", "coordinates": [-114.234, 46.789]}]
    }
  ]
}
```

---

### 6.3 NEO (Near Earth Objects)
- **Endpoint:** `GET /api/nasa/neo`
- **Query Parameters (Both required):**
  - `start_date`: `Y-m-d` format.
  - `end_date`: `Y-m-d` format (must be >= `start_date`).
- **Success Response (200 OK):**
```json
{
  "element_count": 1,
  "near_earth_objects": {
    "2026-07-01": [
      {
        "id": "3542519",
        "name": "(2010 PK9)",
        "is_potentially_hazardous_asteroid": false,
        "close_approach_data": [
          {
            "close_approach_date": "2026-07-01",
            "relative_velocity": {"kilometers_per_hour": "45281.3"},
            "miss_distance": {"kilometers": "7181023.2"}
          }
        ]
      }
    ]
  }
}
```

---

### 6.4 Space Weather (DONKI)
- **Endpoint:** `GET /api/nasa/space-weather`
- **Query Parameters (All required):**
  - `type`: Must be one of `FLR`, `CME`, or `GST`.
  - `start_date`: `Y-m-d` format.
  - `end_date`: `Y-m-d` format (must be >= `start_date`).
- **Success Response (200 OK):**
```json
[
  {
    "flrID": "2026-06-02T13:45:00-FLR-001",
    "beginTime": "2026-06-02T13:45Z",
    "peakTime": "2026-06-02T14:10Z",
    "endTime": "2026-06-02T14:35Z",
    "classType": "M1.2"
  }
]
```

---

## 7. Flutter Integration Blueprint

### 7.1 Dependencies
```yaml
dependencies:
  dio: ^5.4.0
  flutter_secure_storage: ^9.0.0
```

### 7.2 ApiClient with Automatic Token Injection
```dart
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class ApiClient {
  static const String baseUrl = 'http://10.0.2.2:8000/api'; // Android Emulator
  
  final Dio dio = Dio(BaseOptions(
    baseUrl: baseUrl,
    connectTimeout: const Duration(seconds: 10),
    receiveTimeout: const Duration(seconds: 10),
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
  ));

  final _storage = const FlutterSecureStorage();

  ApiClient() {
    dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          final token = await _storage.read(key: 'auth_token');
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          return handler.next(options);
        },
        onError: (DioException e, handler) async {
          if (e.response?.statusCode == 401) {
            await _storage.delete(key: 'auth_token');
          }
          return handler.next(e);
        },
      ),
    );
  }
}
```
