document.addEventListener("DOMContentLoaded", function () {

    const tableBody = document.getElementById("fetchPrograms");

    const API = "ajaxadmin/";

    // =====================================================
    // INIT
    // =====================================================

        loadPrograms();
  

    // =====================================================
    // LOAD PROGRAMS
    // =====================================================
    function loadPrograms() {

    if (!tableBody) return;

    tableBody.innerHTML = `
        <tr><td colspan="6">Loading...</td></tr>
    `;

    fetch(API + "fetchPrograms.php")
        .then(res => res.json())
        .then(data => {

            if (!data.length) {
                tableBody.innerHTML = `
                    <tr><td colspan="6">No data found</td></tr>
                `;
                return;
            }

            let html = "";

            data.forEach(p => {
                let actionBtn = "";

                if (p.ProgramActive == 0) {
                    actionBtn = `<a href="#" class="js-delete-program activate" data-id="${p.ProgramID}">Activate</a>`;
                } else {
                    actionBtn = `<a href="#" class="js-delete-program delete" data-id="${p.ProgramID}">Disable</a>`;
                }
                html += `
                <tr>
                    <td><b>#${p.ProgramID}</b></td>

                    <td style="font-size:16px;font-weight:600;">
                        ${p.ProgramName}
                    </td>

                    <td>
                        <span class="badge">${p.sectionCount}</span>
                    </td>

                    <td>
                        <span class="badge">${p.itemCount}</span>
                    </td>

                    <td>
                        ${p.ProgramActive == 1 
                            ? "<span class='active'>Active</span>" 
                            : "<span class='inactive'>Inactive</span>"}
                    </td>

                    <td>
                        <a href="managePrograms.php?do=view&pid=${p.ProgramID}">View</a>
                        <a href="managePrograms.php?do=edit&pid=${p.ProgramID}">Edit</a>
                        ${actionBtn}
                    </td>
                </tr>
                `;
            });

            tableBody.innerHTML = html;
        })
        .catch(() => {
            tableBody.innerHTML = `
                <tr><td colspan="6">Error loading data</td></tr>
            `;
        });
}

    // =====================================================
    // DELETE PROGRAM
    // =====================================================
    document.addEventListener("click", function (e) {

        const btn = e.target.closest(".js-delete-program");

        if (!btn) return;

        e.preventDefault();

        const id = btn.dataset.id;

        if (!confirm("Are you sure you want to delete this program?")) return;

        fetch(API + "deleteProgram.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "ProgramID=" + encodeURIComponent(id)
        })
        .then(res => res.text())
        .then(res => {

            if (res.trim() === "success") {
                loadPrograms();
            } else {
                alert("Delete failed: " + res);
            }
        })
        .catch(() => alert("Server error"));
    });

    // =====================================================
    // ADD PROGRAM
    // =====================================================
    const addForm = document.getElementById("addProgramForm");

    if (addForm) {

        addForm.addEventListener("submit", function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            fetch(API + "addProgram.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(res => {

                if (res.trim() === "success") {
                    window.location.href = "managePrograms.php?do=manage";
                } else {
                    alert(res);
                }
            })
            .catch(() => alert("Server error"));
        });
    }

    // =====================================================
    // UPDATE PROGRAM
    // =====================================================
    const editForm = document.getElementById("editProgramForm");

    if (editForm) {

        editForm.addEventListener("submit", function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            fetch(API + "updateProgram.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(res => {

                if (res.trim() === "success") {
                    window.location.href = "managePrograms.php?do=manage";
                } else {
                    alert(res);
                }
            })
            .catch(() => alert("Server error"));
        });
    }


    
});