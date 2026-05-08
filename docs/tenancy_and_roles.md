Tenancy Architecture:  Notes
1. The Middleware: The "Bouncer" (Access Control)
Analogy: Think of Middleware as a security guard standing at the front door of your routes.

Roles:

CheckSuperAdmin: Only people with role_id === 1 can enter.

CheckAdminCompany: Only role_id === 2 can enter.

Function: It controls WHO can enter a specific URL. It doesn't look at the data yet; it only checks the user’s "ID card" (role_id).

For example 
    1.1 Middleware (The Login & Entry Check)
      a.  Happens on every single request to a specific route. (update , delete , login and etc..) because when login it already remember the session so everytime we do request it auto check the role ID .
      
      b. Every move after: The Middleware checks the "ID Card" (role_id) before the Controller even starts working.

    Result: It stops the wrong people from entering the room.If it try to access it will display forbidden.

2. The TenantScope: The "Filter" (Data Privacy)

The Filter: It automatically injects a WHERE tenant_id = X clause into every single SELECT query.

For Example 
    2.1 What it does: It automatically modifies every SQL query.

        a. The Logic: Even if you write a simple command like Department::all(), the Scope intercepts it.

        b. The Result:

            b.1 What you wrote: SELECT * FROM departments

            b.2 What actually happens: SELECT * FROM departments WHERE tenant_id = 5

The Backdoor: It includes a smart check: if a Super Admin is logged in, the filter is removed, allowing them to see data globally.

3. The Trait
The Trait is the bridge between your Model (the code) and the Scope (the logic). Without the Trait, the Model wouldn't know the Scope exists.

Key Features:

    A. The Glue (Reading)
        When the Model "boots up" (starts working), the Trait tells it: "Hey, you must use the TenantScope for everything you do." This saves you from having to manually add the scope to every single Model file.

    B. The Auto-Stamper (Creating)
        When you create a new record, the Trait acts like an automatic rubber stamp.

        The Logic: It waits for the creating event.

        The Action: It looks at the person logged in, grabs their tenant_id, and "stamps" it onto the new data before it hits the database.

Why it's great: You don't have to write $request->tenant_id in your Controller. It happens automatically in the background.
addGlobalScope: It ensures that every time the Model talks to the database, it follows the privacy rules (TenantScope).