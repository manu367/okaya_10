<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IndexedDB Vanilla JS – Fixed</title>
    <style>
        body { font-family: system-ui, sans-serif; padding: 20px; }
        input, button { margin: 5px 0; padding: 6px; }
        button:disabled { opacity: 0.5; }
        pre { background: #111; color: #0f0; padding: 10px; }
    </style>
</head>
<body>

<h2>IndexedDB – Vanilla JS (Working)</h2>

<input id="name" placeholder="Name">
<input id="email" placeholder="Email">

<br>

<button id="addBtn" disabled>Add User</button>
<button id="loadBtn" disabled>Load Users</button>

<pre id="output"></pre>

<script>
    /* ===============================
       DATABASE SETUP
    ================================ */

    let db;

    const addBtn  = document.getElementById("addBtn");
    const loadBtn = document.getElementById("loadBtn");

    const request = indexedDB.open("MyAppDB", 1);

    request.onupgradeneeded = function (event) {
        db = event.target.result;

        if (!db.objectStoreNames.contains("users")) {
            const store = db.createObjectStore("users", {
                keyPath: "id",
                autoIncrement: true
            });

            store.createIndex("email", "email", { unique: true });
        }
    };

    request.onsuccess = function (event) {
        db = event.target.result;
        log("Database ready");

        addBtn.disabled  = false;
        loadBtn.disabled = false;
    };

    request.onerror = function () {
        log("DB Error: " + request.error);
    };

    /* ===============================
       EVENTS
    ================================ */

    addBtn.onclick = addUser;
    loadBtn.onclick = loadUsers;

    /* ===============================
       CRUD
    ================================ */

    function addUser() {
        const name  = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();

        if (!name || !email) {
            log("Name and Email required");
            return;
        }

        const tx = db.transaction("users", "readwrite");
        const store = tx.objectStore("users");

        const req = store.add({
            name,
            email,
            role: "user",
            createdAt: Date.now()
        });

        req.onsuccess = () => log("User added successfully");
        req.onerror   = () => log("Add failed: " + req.error);
    }

    function loadUsers() {
        const tx = db.transaction("users", "readonly");
        const store = tx.objectStore("users");

        const req = store.getAll();

        req.onsuccess = () => {
            log(JSON.stringify(req.result, null, 2));
        };
    }

    /* ===============================
       HELPER
    ================================ */

    function log(msg) {
        document.getElementById("output").textContent = msg;
    }
</script>

</body>
</html>
