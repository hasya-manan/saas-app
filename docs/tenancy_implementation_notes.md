# Why We Implement Multi-Tenancy Isolation

To ensure our SaaS application is secure, robust, and correctly isolated, we have implemented a "Defense in Depth" strategy using three distinct layers. Each layer serves a specific purpose in protecting tenant data.

## 1. TenantScope (The "Kill Switch")
**Location:** Global Scope on Models (e.g., `User`, `Tenant`)

The `TenantScope` acts at the database level. It is the first and most critical line of defense.

*   **Purpose:** It automatically filters all database queries to ensure that users only ever "see" data that belongs to their `tenant_id`.
*   **The "Kill Switch":** For users without a valid `tenant_id` (like a regular user whose assignment is corrupted or missing), the scope applies a `whereRaw('1 = 0')` condition. This effectively makes the database return zero results, preventing unauthorized access to any data in the system.
*   **SuperAdmin Bypass:** We explicitly allow SuperAdmins (Role 1) to bypass this scope so they can manage the platform, oversee all tenants, and perform administrative tasks.

## 2. Middleware (The "Gatekeeper")
**Location:** Route Middleware

While the `TenantScope` handles data, the Middleware handles **context**.

*   **Purpose:** It ensures that the application environment is correctly set up for the current request.
*   **Function:** It identifies which tenant the request belongs to (usually via subdomains or session data) and sets the global application state. It ensures that any subsequent code—from the controller to the model—is aware of the "current" tenant.
*   **Security:** It prevents a user from accidentally or maliciously accessing a route belonging to another tenant by verifying their authentication context at the start of every request.

## 3. UserPolicy (The "Verifier")
**Location:** Laravel Policies

The `UserPolicy` handles authorization for specific actions (viewing, updating, deleting) at the application level.

*   **Purpose:** It acts as the final guard against unauthorized actions, providing a layer of protection even if the `TenantScope` is accidentally bypassed (e.g., via `withoutGlobalScopes()`).
*   **Granular Control:** It enforces rules like:
    *   *Can the user view this profile?* Only if the tenant IDs match.
    *   *Can the user delete this user?* Only if it's not themselves and the tenant IDs match.
*   **SuperAdmin Bypass:** The `before` method in the policy acts as a "God Mode" bypass, ensuring SuperAdmins can manage the platform without hitting authorization errors.

---

### Summary: Why all three?
| Layer | Focus | Why we need it |
| :--- | :--- | :--- |
| **TenantScope** | **Data Visibility** | Ensures data from other tenants never leaves the database. |
| **Middleware** | **Request Context** | Ensures the application knows *who* is asking and *where* they are. |
| **UserPolicy** | **Action Authorization** | Ensures that even if a user knows a specific ID, they cannot perform unauthorized actions. |

By layering these three, we create a system where data is **invisible** to those who shouldn't see it and **unmodifiable** by those who shouldn't change it.