<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>Admin Management</title>
    <style>
        #modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 500px;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Restaurant Reservation Management System</h1>
        <nav class="nav-bar">
            <ul>
                <li><a href="logout.php" style="color: red;">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-content" style="max-width: 350px;">
        <section class="content-header">
            <h2>Admin Page</h2>
            <p>Manage User, Table, and Table Type information.</p>
        </section>

        <section class="content-body">
            <div class="user-management">
                <h4>User Management</h4>
                <form id="userForm">
                    <label for="userID">User ID:</label>
                    <input type="text" id="userID" name="userID" placeholder="Enter User ID" required>
                    <div class="buttons">
                        <button type="button" id="editUserBtn">Edit User</button>
                    </div>
                </form>
            </div>

            <div class="Table-management">
                <h4>Table Management</h4>
                <form id="TableForm">
                    <label for="TableID">Table ID:</label>
                    <input type="text" id="TableID" name="TableID" placeholder="Enter Table ID" required>
                    <div class="buttons">
                        <button type="button" id="editTableBtn">Edit Table</button>
                    </div>
                </form>
            </div>

            <div class="Table-type-management">
                <h4>Table Type Management</h4>
                <form id="TableTypeForm">
                    <label for="TableTypeID">Table Type ID:</label>
                    <input type="text" id="TableTypeID" name="TableTypeID" placeholder="Enter Table Type ID" required>
                    <div class="buttons">
                        <button type="button" id="editTableTypeBtn">Edit Table Type</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; DBMS Project Group 6</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('editUserBtn').addEventListener('click', handleEditUser);
            document.getElementById('editTableBtn').addEventListener('click', handleEditTable);
            document.getElementById('editTableTypeBtn').addEventListener('click', handleEditTableType);
        });

        const Modal = {
            show: function(title, content) {
                const modal = document.createElement("div");
                modal.id = "modal";
                modal.innerHTML = `
                    <div class="modal-content">
                        <h3>${title}</h3>
                        ${content}
                        <button id="closeModalBtn">Close</button>
                    </div>`;
                document.body.appendChild(modal);
                document.getElementById('closeModalBtn').addEventListener('click', this.close);
            },
            close: function() {
                const modal = document.getElementById("modal");
                if (modal) modal.remove();
            }
        };

        const ApiService = {
            fetch: async function(url) {
                try {
                    const response = await fetch(url);
                    return await response.json();
                } catch (error) {
                    console.error('API fetch error:', error);
                    return { error: 'Failed to connect to server' };
                }
            },
            post: async function(url, formData) {
                try {
                    const response = await fetch(url, {
                        method: "POST",
                        body: formData
                    });
                    return await response.json();
                } catch (error) {
                    console.error('API post error:', error);
                    return { error: 'Failed to connect to server' };
                }
            }
        };

        const UserManager = {
            edit: async function() {
                const userID = document.getElementById("userID").value;
                if (!userID) {
                    alert("Please enter a User ID.");
                    return;
                }

                const data = await ApiService.fetch(`admin_actions.php?action=fetch_user&userID=${userID}`);
                if (data.error) {
                    alert(data.error);
                    return;
                }

                const form = `
                    <form id="editUserForm">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="${data.name}" required><br>

                        <label for="balance">Balance:</label>
                        <input type="number" id="balance" name="balance" value="${data.balance}" required><br>

                        <label for="user_type">User Type:</label>
                        <input type="text" id="user_type" name="user_type" value="${data.user_type}" required><br>

                        <button type="button" id="saveUserBtn">Save</button>
                    </form>`;
                
                Modal.show("Edit User", form);
                document.getElementById('saveUserBtn').addEventListener('click', () => this.submit(userID));
            },
            submit: async function(userID) {
                const formData = new FormData(document.getElementById("editUserForm"));
                formData.append("action", "update_user");
                formData.append("userID", userID);

                const data = await ApiService.post("admin_actions.php", formData);
                if (data.success) {
                    alert("User updated successfully!");
                    Modal.close();
                } else {
                    alert(data.error || "Failed to update user.");
                }
            }
        };

        const TableManager = {
            edit: async function() {
                const TableID = document.getElementById("TableID").value;
                if (!TableID) {
                    alert("Please enter a Table ID.");
                    return;
                }

                console.log("TableID:", TableID);

                const data = await ApiService.fetch(`admin_actions.php?action=fetch_Table&tableID=${TableID}`);
                if (data.error) {
                    alert(data.error);
                    return;
                }

                const form = `
                    <form id="editTableForm">
                        <label for="clean_status">Clean Status:</label>
                        <input type="text" id="clean_status" name="clean_status" value="${data.clean_status}" required><br>

                        <button type="button" id="saveTableBtn">Save</button>
                    </form>`;
                
                Modal.show("Edit Table", form);
                document.getElementById('saveTableBtn').addEventListener('click', () => this.submit(TableID));
            },
            submit: async function(TableID) {
                const formData = new FormData(document.getElementById("editTableForm"));
                formData.append("action", "update_Table");
                formData.append("tableID", TableID); 

                const data = await ApiService.post("admin_actions.php", formData);
                if (data.success) {
                    alert("Table updated successfully!");
                    Modal.close();
                } else {
                    alert(data.error || "Failed to update Table.");
                }
            }
        };

        const TableTypeManager = {
            edit: async function() {
                const TableTypeID = document.getElementById("TableTypeID").value;
                if (!TableTypeID) {
                    alert("Please enter a Table Type ID.");
                    return;
                }

                const data = await ApiService.fetch(`admin_actions.php?action=fetch_Table_type&TableTypeID=${TableTypeID}`);
                if (data.error) {
                    alert(data.error);
                    return;
                }

                const form = `
                    <form id="editTableTypeForm">
                        <label for="introduction">Introduction:</label>
                        <textarea id="introduction" name="introduction" required>${data.introduction}</textarea><br>

                        <label for="price">Price:</label>
                        <input type="number" id="price" name="price" value="${data.price}" required><br>

                        <label for="remain">Remaining Tables:</label>
                        <input type="number" id="remain" name="remain" value="${data.remain}" required><br>

                        <button type="button" id="saveTableTypeBtn">Save</button>
                    </form>`;
                
                Modal.show("Edit Table Type", form);
                document.getElementById('saveTableTypeBtn').addEventListener('click', () => this.submit(TableTypeID));
            },
            submit: async function(TableTypeID) {
                const formData = new FormData(document.getElementById("editTableTypeForm"));
                formData.append("action", "update_Table_type");
                formData.append("TableTypeID", TableTypeID);

                const data = await ApiService.post("admin_actions.php", formData);
                if (data.success) {
                    alert("Table Type updated successfully!");
                    Modal.close();
                } else {
                    alert(data.error || "Failed to update Table type.");
                }
            }
        };

        function handleEditUser() {
            UserManager.edit();
        }

        function handleEditTable() {
            TableManager.edit();
        }

        function handleEditTableType() {
            TableTypeManager.edit();
        }
    </script>
</body>
</html>