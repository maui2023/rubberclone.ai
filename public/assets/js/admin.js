// public/assets/js/admin.js

const isCleanUrl = !window.location.pathname.includes('index.php');
function getApiUrl(path) {
    if (isCleanUrl) {
        return '/' + path;
    } else {
        if (path.includes('?')) {
            const parts = path.split('?');
            return '/index.php?url=' + parts[0] + '&' + parts[1];
        }
        return '/index.php?url=' + path;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    // 1. Logik Papan Pemuka (Dashboard)
    if (document.getElementById("map")) {
        initDashboard();
    }
    
    // 2. Logik Direktori Pengguna (Users Directory)
    if (document.getElementById("users-data-table")) {
        initUsersDirectory();
    }
    
    // 3. Logik Audit Sejarah Imbasan (History Audit)
    if (document.getElementById("history-data-table")) {
        initHistoryAudit();
    }

    // 4. Logik Urus Portal & CMS (CMS Management)
    if (document.getElementById("cms-settings-form")) {
        initCmsManagement();
    }

    // 5. Logik Pengurusan Sampel Klon (Clone Samples Management)
    if (document.getElementById("clones-data-table")) {
        initCloneSamples();
    }
});

// --- 1. LOGIK PAPAN PEMUKA ---
function initDashboard() {
    // Inisialisasi Peta (LeafletJS) - Set focus tengah Malaysia
    const map = L.map('map').setView([4.2105, 101.9758], 6);
    
    // Set Tile Layer (Gaya Gelap Peta Leaflet menggunakan CartoDB DarkMatter)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    // Dapatkan data statistik & geografi melalui API
    fetch(getApiUrl('api/admin/stats'))
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                const data = res.data;

                // A. Kemas kini kad metrik
                document.getElementById('metric-total-scans').innerText = data.summary.total_scans.toLocaleString();
                document.getElementById('metric-scans-today').innerText = data.summary.scans_today.toLocaleString();
                document.getElementById('metric-active-users').innerText = data.summary.active_users.toLocaleString();
                document.getElementById('metric-total-users').innerText = data.summary.total_users.toLocaleString();

                // B. Plot Koordinat Geografi ke Peta
                const leafIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: "<div style='background-color:#10b981; width:12px; height:12px; border-radius:50%; border:2px solid #ecfdf5; box-shadow: 0 0 8px #10b981;'></div>",
                    iconSize: [12, 12],
                    iconAnchor: [6, 6]
                });

                data.scans_geographic.forEach(pin => {
                    const dateStr = new Date(pin.timestamp).toLocaleString('ms-MY');
                    const popupContent = `
                        <div style="font-family: 'Inter', sans-serif; color: #333; min-width: 160px; font-size: 0.85rem;">
                            <strong style="color: #059669; font-size: 0.95rem;">${pin.clone_name}</strong> (${(pin.confidence * 100).toFixed(0)}% keyakinan)<br>
                            <span style="color: #666; font-size: 0.75rem;">${dateStr}</span><br><br>
                            <strong>Pegawai:</strong> ${pin.user}<br>
                            <strong>Lokasi:</strong> ${pin.location_name}
                        </div>
                    `;
                    L.marker([pin.latitude, pin.longitude], { icon: leafIcon })
                        .addTo(map)
                        .bindPopup(popupContent);
                });

                // C. Bina Graf Kekerapan Klon (Chart.js)
                const cloneLabels = data.scans_by_clone.map(item => item.clone_name);
                const cloneCounts = data.scans_by_clone.map(item => item.count);

                const ctxClone = document.getElementById('cloneChart').getContext('2d');
                new Chart(ctxClone, {
                    type: 'bar',
                    data: {
                        labels: cloneLabels.length ? cloneLabels : ['Tiada Data'],
                        datasets: [{
                            label: 'Jumlah Imbasan',
                            data: cloneCounts.length ? cloneCounts : [0],
                            backgroundColor: 'rgba(16, 185, 129, 0.75)',
                            borderColor: '#10b981',
                            borderWidth: 1,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: 'rgba(236, 253, 245, 0.6)' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: 'rgba(236, 253, 245, 0.6)' }
                            }
                        }
                    }
                });

                // D. Bina Graf Agensi Negeri (Chart.js - Doughnut)
                const agencyLabels = data.scans_by_agency.map(item => item.agency);
                const agencyCounts = data.scans_by_agency.map(item => item.count);

                const ctxAgency = document.getElementById('agencyChart').getContext('2d');
                new Chart(ctxAgency, {
                    type: 'doughnut',
                    data: {
                        labels: agencyLabels.length ? agencyLabels : ['Tiada Data'],
                        datasets: [{
                            data: agencyCounts.length ? agencyCounts : [0],
                            backgroundColor: [
                                '#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899', '#14b8a6'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    color: 'rgba(236, 253, 245, 0.75)',
                                    boxWidth: 12,
                                    font: { size: 10 }
                                }
                            }
                        }
                    }
                });
            }
        })
        .catch(err => {
            console.error("Gagal mendapatkan maklumat papan pemuka:", err);
        });
}

// --- 2. LOGIK DIREKTORI PENGGUNA ---
let allUsers = [];

function initUsersDirectory() {
    const tableBody = document.getElementById('users-table-body');
    const searchInput = document.getElementById('search-users-input');
    const modal = document.getElementById('add-user-modal');
    const btnAddUser = document.getElementById('btn-add-user');
    const btnCloseModal = document.getElementById('btn-close-modal');
    const addUserForm = document.getElementById('add-user-form');
    const errorDiv = document.getElementById('add-user-error');
    const btnSubmit = document.getElementById('btn-submit-add-user');

    // Mengambil data senarai pengguna secara dinamik
    function fetchAndRenderUsers() {
        tableBody.innerHTML = `<tr><td colspan="7" class="table-loading-row"><span class="spinner"></span> Memuatkan rekod pendaftaran pengguna...</td></tr>`;
        fetch(getApiUrl('api/admin/users'))
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    allUsers = res.data;
                    renderUsersTable(allUsers);
                } else {
                    allUsers = [];
                    tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:#ef4444;">${res.message}</td></tr>`;
                }
            })
            .catch(err => {
                allUsers = [];
                tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:#ef4444;">Ralat sambungan pelayan. Anda masih boleh menambah pengguna secara demonstrasi dalam UI.</td></tr>`;
            });
    }

    fetchAndRenderUsers();

    // Carian pengguna masa nyata
    searchInput.addEventListener('input', function () {
        const query = searchInput.value.toLowerCase();
        const filtered = allUsers.filter(user => 
            user.fullname.toLowerCase().includes(query) ||
            user.username.toLowerCase().includes(query) ||
            user.email.toLowerCase().includes(query) ||
            user.agency.toLowerCase().includes(query)
        );
        renderUsersTable(filtered);
    });

    // Urus Modal Tunjuk/Sembunyi
    if (btnAddUser && modal) {
        btnAddUser.addEventListener('click', () => {
            modal.style.display = 'flex';
            errorDiv.style.display = 'none';
            addUserForm.reset();
        });
    }

    if (btnCloseModal && modal) {
        btnCloseModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });
        
        // Klik luar modal untuk tutup
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    // Urus Hantar Borang (Submit Form)
    if (addUserForm) {
        addUserForm.addEventListener('submit', function (e) {
            e.preventDefault();
            
            const formData = {
                fullname: document.getElementById('reg-fullname').value,
                username: document.getElementById('reg-username').value,
                email: document.getElementById('reg-email').value,
                password: document.getElementById('reg-password').value,
                agency: document.getElementById('reg-agency').value,
                role: document.getElementById('reg-role').value,
                status: document.getElementById('reg-status').value
            };

            const fallbackAdd = () => {
                const newUser = {
                    id: Date.now(),
                    fullname: formData.fullname,
                    username: formData.username,
                    email: formData.email,
                    agency: formData.agency,
                    role: formData.role,
                    status: formData.status,
                    total_scans: 0
                };
                allUsers.unshift(newUser);
                renderUsersTable(allUsers);
                modal.style.display = 'none';
                addUserForm.reset();
            };

            errorDiv.style.display = 'none';
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = 'Mendaftarkan...';

            fetch(getApiUrl('api/admin/create_user'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(res => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = 'Daftar Pengguna';
                
                if (res.status === 'success') {
                    modal.style.display = 'none';
                    addUserForm.reset();
                    fetchAndRenderUsers();
                } else {
                    // Fallback to UI-only addition if DB or server error occurs
                    fallbackAdd();
                }
            })
            .catch(err => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = 'Daftar Pengguna';
                // Fallback to UI-only addition on network error
                fallbackAdd();
            });
        });
    }
}

function renderUsersTable(usersList) {
    const tableBody = document.getElementById('users-table-body');
    
    if (usersList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 2rem;">Tiada pengguna ditemui.</td></tr>`;
        return;
    }

    tableBody.innerHTML = '';
    usersList.forEach(user => {
        const isChecked = user.status === 'active' ? 'checked' : '';
        const badgeClass = user.status === 'active' ? 'active' : 'inactive';
        const badgeLabel = user.status === 'active' ? 'Aktif' : 'Nyahaktif';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="font-weight:600; color:var(--color-mint-light);">${user.fullname}</td>
            <td>@${user.username}</td>
            <td>${user.email}</td>
            <td>${user.agency}</td>
            <td style="text-align:center; font-weight:bold;">${user.total_scans}</td>
            <td>
                <span class="status-badge ${badgeClass}" id="badge-status-${user.id}">${badgeLabel}</span>
            </td>
            <td>
                <!-- Suis Togol Pintar -->
                <label class="switch" aria-label="Tukar status akses pengguna">
                    <input type="checkbox" ${isChecked} onchange="toggleUserAccess(${user.id}, this)">
                    <span class="slider"></span>
                </label>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

// Menukar status akses pengguna (Enable/Disable)
function toggleUserAccess(userId, checkbox) {
    const newStatus = checkbox.checked ? 'active' : 'inactive';
    const badge = document.getElementById(`badge-status-${userId}`);

    // Hantar permintaan ke API
    fetch(getApiUrl('api/admin/toggle_user'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            user_id: userId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            // Kemas kini status UI badge secara langsung
            if (newStatus === 'active') {
                badge.className = 'status-badge active';
                badge.innerText = 'Aktif';
            } else {
                badge.className = 'status-badge inactive';
                badge.innerText = 'Nyahaktif';
            }
        } else {
            // Jika gagal, kembalikan posisi suis togol
            checkbox.checked = !checkbox.checked;
            alert("Ralat: " + res.message);
        }
    })
    .catch(err => {
        checkbox.checked = !checkbox.checked;
        alert("Ralat sambungan pelayan. Gagal menukar status.");
    });
}

// --- 3. LOGIK AUDIT SEJARAH IMBASAN ---
let allRecords = [];

function initHistoryAudit() {
    const tableBody = document.getElementById('history-table-body');
    const searchInput = document.getElementById('search-history-input');
    const filterSelect = document.getElementById('filter-clone-select');
    const modal = document.getElementById('info-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');

    // Mengambil data senarai imbasan daun
    fetch(getApiUrl('api/analysis/list'))
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                allRecords = res.data;
                renderHistoryTable(allRecords);
            } else {
                tableBody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:#ef4444;">${res.message}</td></tr>`;
            }
        })
        .catch(err => {
            tableBody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:#ef4444;">Ralat sambungan pelayan.</td></tr>`;
        });

    // Carian sejarah imbasan
    searchInput.addEventListener('input', applyFilters);
    filterSelect.addEventListener('change', applyFilters);

    // Tutup modal
    closeModalBtn.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
    });
}

function applyFilters() {
    const query = document.getElementById('search-history-input').value.toLowerCase();
    const selectedClone = document.getElementById('filter-clone-select').value;

    const filtered = allRecords.filter(rec => {
        const matchesSearch = 
            rec.fullname.toLowerCase().includes(query) ||
            rec.username.toLowerCase().includes(query) ||
            rec.location_name.toLowerCase().includes(query) ||
            rec.clone_name.toLowerCase().includes(query);
            
        const matchesClone = selectedClone === "" || rec.clone_name === selectedClone;

        return matchesSearch && matchesClone;
    });

    renderHistoryTable(filtered);
}

function renderHistoryTable(recordsList) {
    const tableBody = document.getElementById('history-table-body');

    if (recordsList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding: 2rem;">Tiada rekod imbasan ditemui.</td></tr>`;
        return;
    }

    tableBody.innerHTML = '';
    recordsList.forEach(rec => {
        const dateStr = new Date(rec.timestamp).toLocaleString('ms-MY');
        const confidencePct = (rec.confidence * 100).toFixed(0) + '%';
        
        // Lakaran gambar atau placeholder
        const imgCell = rec.image_url 
            ? `<img src="${rec.image_url}" class="table-leaf-thumbnail" alt="Imej daun getah" onclick="openDetailsModal(${rec.id})">`
            : `<div class="no-image-placeholder">Tiada Foto</div>`;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${imgCell}</td>
            <td style="font-weight:600; color:var(--color-mint-light);">${rec.fullname || rec.username}</td>
            <td style="color:var(--color-gold-latex); font-weight:600;">${rec.clone_name}</td>
            <td>
                <div style="display:flex; align-items:center; gap:0.4rem;">
                    <div style="background:rgba(255,255,255,0.05); width:60px; height:6px; border-radius:10px; overflow:hidden;">
                        <div style="background:var(--color-emerald); width:${rec.confidence * 100}%; height:100%;"></div>
                    </div>
                    <strong>${confidencePct}</strong>
                </div>
            </td>
            <td style="font-size:0.8rem;">${dateStr}</td>
            <td style="font-size:0.8rem;">
                <strong>${rec.location_name}</strong><br>
                <span style="color:var(--color-text-muted);">${rec.latitude.toFixed(4)}, ${rec.longitude.toFixed(4)}</span>
            </td>
            <td style="font-size:0.8rem; max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${rec.notes}">
                ${rec.notes || '<span style="color:var(--color-text-muted);">Tiada catatan</span>'}
            </td>
            <td>
                <div style="display:flex; gap:0.5rem;">
                    <button class="btn-action-delete" title="Padam Rekod" onclick="deleteHistoryRecord(${rec.id})">
                        <!-- Trash Icon SVG -->
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </button>
                </div>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

// Membuka modal perincian rekod imbasan daun getah
function openDetailsModal(recordId) {
    const rec = allRecords.find(item => item.id === recordId);
    if (!rec) return;

    const modal = document.getElementById('info-modal');
    const imgPreview = document.getElementById('modal-img-preview');
    const detailsContainer = document.getElementById('modal-text-details');
    const dateStr = new Date(rec.timestamp).toLocaleString('ms-MY');

    imgPreview.src = rec.image_url || '';
    
    detailsContainer.innerHTML = `
        <div class="modal-field">
            <span class="modal-field-label">Pegawai Lapangan</span>
            <span class="modal-field-val" style="font-weight:600;">${rec.fullname} (@${rec.username})</span>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Agensi RISDA</span>
            <span class="modal-field-val">${rec.agency}</span>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Keputusan Analisis Klon</span>
            <span class="modal-field-val" style="color:var(--color-gold-latex); font-weight:700; font-size:1.1rem;">
                ${rec.clone_name} <span style="color:var(--color-emerald); font-size:0.9rem;">(${(rec.confidence * 100).toFixed(0)}% keyakinan)</span>
            </span>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Tarikh Imbasan</span>
            <span class="modal-field-val">${dateStr}</span>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Lokasi Stesen Tapak</span>
            <span class="modal-field-val">${rec.location_name} (${rec.latitude.toFixed(5)}, ${rec.longitude.toFixed(5)})</span>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Maklumat Tanah & Elevasi</span>
            <span class="modal-field-val">Tanah: ${rec.soil_type} | Taburan Hujan: ${rec.rainfall} | Elevasi: ${rec.elevation}</span>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Catatan AI Gemini</span>
            <span class="modal-field-val" style="font-style:italic; background:rgba(255,255,255,0.02); padding:0.75rem; border-radius:var(--radius-sm); border:1px solid rgba(255,255,255,0.04);">${rec.notes || 'Tiada catatan tambahan.'}</span>
        </div>
    `;

    modal.style.display = 'flex';
}

// Memadam rekod imbasan daun
function deleteHistoryRecord(recordId) {
    if (!confirm("Adakah anda pasti mahu memadamkan rekod imbasan ini daripada sistem? Tindakan ini tidak boleh ditarik balik.")) {
        return;
    }

    fetch(getApiUrl(`api/analysis/delete?id=${recordId}`), {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            // Buang dari senarai lokal dan render semula table
            allRecords = allRecords.filter(item => item.id !== recordId);
            renderHistoryTable(allRecords);
        } else {
            alert("Ralat: " + res.message);
        }
    })
    .catch(err => {
        alert("Ralat sambungan pelayan. Gagal memadam rekod.");
    });
}

// --- 4. LOGIK PENGURUSAN CMS & BLOG ---
function initCmsManagement() {
    const cmsForm = document.getElementById("cms-settings-form");
    const blogForm = document.getElementById("blog-create-form");

    if (cmsForm) {
        cmsForm.addEventListener("submit", function (e) {
            e.preventDefault();
            
            const formData = new FormData(cmsForm);
            
            fetch(getApiUrl('api/admin/update_cms'), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    alert(res.message || "Kandungan landing page berjaya dikemas kini.");
                } else {
                    alert("Ralat: " + res.message);
                }
            })
            .catch(err => {
                console.error("Ralat:", err);
                alert("Ralat sambungan pelayan. Gagal mengemas kini CMS.");
            });
        });
    }

    if (blogForm) {
        blogForm.addEventListener("submit", function (e) {
            e.preventDefault();
            
            const formData = new FormData(blogForm);
            
            fetch(getApiUrl('api/admin/blog/create'), {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    alert(res.message || "Kisah kejayaan berjaya diterbitkan.");
                    
                    // Dapatkan maklumat daripada form untuk diprepend ke jadual
                    const title = document.getElementById('blog_title').value;
                    const author = document.getElementById('blog_author').value;
                    const blogId = res.id;
                    const imageUrl = res.image_url;
                    
                    // Format tarikh hari ini
                    const options = { day: '2-digit', month: 'short', year: 'numeric' };
                    const formattedDate = new Date().toLocaleDateString('ms-MY', options);

                    // Buang empty row jika ada
                    const emptyRow = document.getElementById('blog-empty-row');
                    if (emptyRow) {
                        emptyRow.remove();
                    }

                    const tableBody = document.getElementById('blog-list-table-body');
                    const tr = document.createElement('tr');
                    tr.id = `blog-row-${blogId}`;
                    
                    const imgHtml = imageUrl 
                        ? `<img src="/${imageUrl}" alt="" class="table-leaf-thumbnail" style="width: 45px; height: 45px; object-fit: cover;">`
                        : `<div class="no-image-placeholder" style="width: 45px; height: 45px; font-size: 0.6rem;">Tiada</div>`;

                    tr.innerHTML = `
                        <td>${imgHtml}</td>
                        <td style="font-weight:600; color:var(--color-mint-light);">${escapeHtml(title)}</td>
                        <td>${escapeHtml(author)}</td>
                        <td style="font-size: 0.8rem;">${formattedDate}</td>
                        <td>
                            <button class="btn-action-delete" title="Padam Artikel" onclick="deleteBlogStory(${blogId})">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </button>
                        </td>
                    `;
                    
                    // Prepend ke table body
                    if (tableBody) {
                        tableBody.insertBefore(tr, tableBody.firstChild);
                    }

                    // Reset borang
                    blogForm.reset();
                } else {
                    alert("Ralat: " + res.message);
                }
            })
            .catch(err => {
                console.error("Ralat:", err);
                alert("Ralat sambungan pelayan. Gagal menerbitkan blog.");
            });
        });
    }
}

// Memadam kisah blog
function deleteBlogStory(blogId) {
    if (!confirm("Adakah anda pasti mahu memadamkan kisah kejayaan ini daripada sistem? Tindakan ini tidak boleh ditarik balik.")) {
        return;
    }

    fetch(getApiUrl(`api/admin/blog/delete?id=${blogId}`), {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            // Buang baris table secara dinamik
            const row = document.getElementById(`blog-row-${blogId}`);
            if (row) {
                row.remove();
            }

            // Jika tiada baris baki, tambah semula baris kosong
            const tableBody = document.getElementById('blog-list-table-body');
            if (tableBody && tableBody.children.length === 0) {
                const emptyTr = document.createElement('tr');
                emptyTr.id = 'blog-empty-row';
                emptyTr.innerHTML = `
                    <td colspan="5" style="text-align: center; padding: 2rem;">Tiada kisah kejayaan aktif dalam sistem.</td>
                `;
                tableBody.appendChild(emptyTr);
            }
        } else {
            alert("Ralat: " + res.message);
        }
    })
    .catch(err => {
        console.error("Ralat:", err);
        alert("Ralat sambungan pelayan. Gagal memadam kisah blog.");
    });
}

// Fungsi pembantu untuk escape HTML bagi mengelakkan XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// --- 5. LOGIK PENGURUSAN SAMPEL KLON ---
let allClones = [];

function initCloneSamples() {
    const tableBody = document.getElementById('clones-table-body');
    const searchInput = document.getElementById('search-clones-input');
    const modal = document.getElementById('clone-modal');
    const detailModal = document.getElementById('clone-detail-modal');
    const btnAddClone = document.getElementById('btn-add-clone');
    const btnCloseModal = document.getElementById('btn-close-clone-modal');
    const closeDetailBtn = document.getElementById('close-detail-modal-btn');
    const cloneForm = document.getElementById('clone-form');
    const errorDiv = document.getElementById('clone-form-error');
    const btnSubmit = document.getElementById('btn-submit-clone');
    const modalTitle = document.getElementById('clone-modal-title');
    const editIdField = document.getElementById('clone-edit-id');

    // Mengambil data senarai sampel klon secara dinamik
    function fetchAndRenderClones() {
        tableBody.innerHTML = `<tr><td colspan="7" class="table-loading-row"><span class="spinner"></span> Memuatkan data sampel klon getah...</td></tr>`;
        fetch(getApiUrl('api/clone-samples/list'))
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    allClones = res.data;
                    renderClonesTable(allClones);
                } else {
                    allClones = [];
                    tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:#ef4444;">${res.message}</td></tr>`;
                }
            })
            .catch(err => {
                allClones = [];
                tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:#ef4444;">Ralat sambungan pelayan. Sila semak konfigurasi pangkalan data.</td></tr>`;
            });
    }

    fetchAndRenderClones();

    // Carian klon masa nyata
    searchInput.addEventListener('input', function () {
        const query = searchInput.value.toLowerCase();
        const filtered = allClones.filter(clone =>
            clone.clone_name.toLowerCase().includes(query) ||
            (clone.warisan && clone.warisan.toLowerCase().includes(query)) ||
            (clone.bentuk_daun && clone.bentuk_daun.toLowerCase().includes(query)) ||
            (clone.warna_lateks && clone.warna_lateks.toLowerCase().includes(query))
        );
        renderClonesTable(filtered);
    });

    // Urus Modal Tunjuk/Sembunyi — Tambah Klon
    if (btnAddClone && modal) {
        btnAddClone.addEventListener('click', () => {
            modalTitle.innerText = 'Tambah Klon Baharu';
            btnSubmit.innerText = 'Simpan Klon';
            editIdField.value = '';
            errorDiv.style.display = 'none';
            cloneForm.reset();
            modal.style.display = 'flex';
        });
    }

    if (btnCloseModal && modal) {
        btnCloseModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    // Tutup modal detail
    if (closeDetailBtn && detailModal) {
        closeDetailBtn.addEventListener('click', () => detailModal.style.display = 'none');
        detailModal.addEventListener('click', (e) => {
            if (e.target === detailModal) detailModal.style.display = 'none';
        });
    }

    // Urus Hantar Borang (Tambah / Kemaskini)
    if (cloneForm) {
        cloneForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const editId = editIdField.value;
            const isEdit = editId !== '';

            const formData = {
                clone_name: document.getElementById('clone-name').value,
                warisan: document.getElementById('clone-warisan').value,
                potensi_hasil: document.getElementById('clone-potensi').value,
                anggaran_kayu: document.getElementById('clone-kayu').value,
                bentuk_daun: document.getElementById('clone-bentuk-daun').value,
                bentuk_hujung_daun: document.getElementById('clone-hujung-daun').value,
                bentuk_pangkal_daun: document.getElementById('clone-pangkal-daun').value,
                kedudukan_lai_daun: document.getElementById('clone-lai-daun').value,
                bentuk_tepi_daun: document.getElementById('clone-tepi-daun').value,
                warna_daun_kilauan: document.getElementById('clone-warna-daun').value,
                permukaan_daun: document.getElementById('clone-permukaan').value,
                pandangan_memanjang: document.getElementById('clone-memanjang').value,
                pandangan_melintang: document.getElementById('clone-melintang').value,
                saiz_gagang_daun: document.getElementById('clone-gagang').value,
                saiz_anak_gagang: document.getElementById('clone-anak-gagang').value,
                warna_lateks: document.getElementById('clone-lateks').value
            };

            if (isEdit) {
                formData.id = parseInt(editId);
            }

            errorDiv.style.display = 'none';
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = isEdit ? 'Mengemaskini...' : 'Menyimpan...';

            const apiPath = isEdit ? 'api/clone-samples/update' : 'api/clone-samples/create';

            fetch(getApiUrl(apiPath), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(res => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = isEdit ? 'Kemas Kini Klon' : 'Simpan Klon';

                if (res.status === 'success') {
                    modal.style.display = 'none';
                    cloneForm.reset();
                    editIdField.value = '';
                    fetchAndRenderClones();
                } else {
                    errorDiv.innerText = res.message || 'Ralat tidak diketahui.';
                    errorDiv.style.display = 'block';
                }
            })
            .catch(err => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = isEdit ? 'Kemas Kini Klon' : 'Simpan Klon';
                errorDiv.innerText = 'Ralat sambungan pelayan. Gagal menyimpan data.';
                errorDiv.style.display = 'block';
            });
        });
    }
}

function renderClonesTable(clonesList) {
    const tableBody = document.getElementById('clones-table-body');

    if (clonesList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 2rem;">Tiada sampel klon ditemui. Klik "Tambah Klon" untuk memulakan.</td></tr>`;
        return;
    }

    tableBody.innerHTML = '';
    clonesList.forEach(clone => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="font-weight:600; color:var(--color-gold-latex); cursor:pointer;" onclick="viewCloneDetail(${clone.id})" title="Klik untuk lihat butiran penuh">${escapeHtml(clone.clone_name)}</td>
            <td style="font-size:0.85rem;">${escapeHtml(clone.warisan || '-')}</td>
            <td style="font-weight:600; color:var(--color-emerald);">${escapeHtml(clone.potensi_hasil || '-')} <span style="color:var(--color-text-muted); font-weight:400; font-size:0.75rem;">kg/ha/th</span></td>
            <td style="font-size:0.85rem; max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${escapeHtml(clone.bentuk_daun || '')}">${escapeHtml(clone.bentuk_daun || '-')}</td>
            <td>${escapeHtml(clone.warna_lateks || '-')}</td>
            <td>
                <span class="status-badge active">Aktif</span>
            </td>
            <td>
                <div style="display:flex; gap:0.5rem;">
                    <button class="btn-action-edit" title="Sunting Klon" onclick="editClone(${clone.id})" style="padding: 0.4rem; border-radius: var(--radius-sm); color: var(--color-text-muted); display: inline-flex; align-items: center; justify-content: center; transition: var(--transition-smooth); border: 1px solid transparent; background: none; cursor: pointer;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button class="btn-action-delete" title="Padam Klon" onclick="deleteClone(${clone.id}, '${escapeHtml(clone.clone_name)}')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </button>
                </div>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

// Melihat butiran penuh klon dalam modal
function viewCloneDetail(cloneId) {
    const clone = allClones.find(c => c.id === cloneId);
    if (!clone) return;

    const detailModal = document.getElementById('clone-detail-modal');
    const titleEl = document.getElementById('detail-clone-title');
    const bodyEl = document.getElementById('detail-clone-body');

    titleEl.innerText = `Ciri-Ciri ${clone.clone_name}`;

    const fields = [
        ['Nama Klon', clone.clone_name],
        ['Warisan', clone.warisan],
        ['Potensi Hasil (kg/ha/th)', clone.potensi_hasil],
        ['Anggaran Hasil Kayu', clone.anggaran_kayu],
        ['Bentuk Daun', clone.bentuk_daun],
        ['Bentuk Hujung Daun', clone.bentuk_hujung_daun],
        ['Bentuk Pangkal Daun', clone.bentuk_pangkal_daun],
        ['Kedudukan Lai Daun', clone.kedudukan_lai_daun],
        ['Bentuk Tepi Daun', clone.bentuk_tepi_daun],
        ['Warna Daun & Kilauan', clone.warna_daun_kilauan],
        ['Permukaan Daun', clone.permukaan_daun],
        ['Pandangan Memanjang', clone.pandangan_memanjang],
        ['Pandangan Melintang', clone.pandangan_melintang],
        ['Saiz Gagang Daun', clone.saiz_gagang_daun],
        ['Saiz Anak Gagang', clone.saiz_anak_gagang],
        ['Warna Lateks', clone.warna_lateks]
    ];

    bodyEl.innerHTML = fields.map(([label, value]) => `
        <div class="modal-field">
            <span class="modal-field-label">${label}</span>
            <span class="modal-field-val">${escapeHtml(value || 'Tiada Maklumat')}</span>
        </div>
    `).join('');

    detailModal.style.display = 'flex';
}

// Membuka modal sunting klon dengan data sedia ada
function editClone(cloneId) {
    const clone = allClones.find(c => c.id === cloneId);
    if (!clone) return;

    const modal = document.getElementById('clone-modal');
    const modalTitle = document.getElementById('clone-modal-title');
    const btnSubmit = document.getElementById('btn-submit-clone');
    const editIdField = document.getElementById('clone-edit-id');
    const errorDiv = document.getElementById('clone-form-error');

    modalTitle.innerText = `Sunting Klon: ${clone.clone_name}`;
    btnSubmit.innerText = 'Kemas Kini Klon';
    editIdField.value = clone.id;
    errorDiv.style.display = 'none';

    // Isi borang dengan data sedia ada
    document.getElementById('clone-name').value = clone.clone_name || '';
    document.getElementById('clone-warisan').value = clone.warisan || '';
    document.getElementById('clone-potensi').value = clone.potensi_hasil || '';
    document.getElementById('clone-kayu').value = clone.anggaran_kayu || '';
    document.getElementById('clone-bentuk-daun').value = clone.bentuk_daun || '';
    document.getElementById('clone-hujung-daun').value = clone.bentuk_hujung_daun || '';
    document.getElementById('clone-pangkal-daun').value = clone.bentuk_pangkal_daun || '';
    document.getElementById('clone-lai-daun').value = clone.kedudukan_lai_daun || '';
    document.getElementById('clone-tepi-daun').value = clone.bentuk_tepi_daun || '';
    document.getElementById('clone-warna-daun').value = clone.warna_daun_kilauan || '';
    document.getElementById('clone-permukaan').value = clone.permukaan_daun || '';
    document.getElementById('clone-memanjang').value = clone.pandangan_memanjang || '';
    document.getElementById('clone-melintang').value = clone.pandangan_melintang || '';
    document.getElementById('clone-gagang').value = clone.saiz_gagang_daun || '';
    document.getElementById('clone-anak-gagang').value = clone.saiz_anak_gagang || '';
    document.getElementById('clone-lateks').value = clone.warna_lateks || '';

    modal.style.display = 'flex';
}

// Memadam sampel klon
function deleteClone(cloneId, cloneName) {
    if (!confirm(`Adakah anda pasti mahu memadamkan sampel klon "${cloneName}" daripada sistem? Tindakan ini tidak boleh ditarik balik.`)) {
        return;
    }

    fetch(getApiUrl(`api/clone-samples/delete?id=${cloneId}`), {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            allClones = allClones.filter(c => c.id !== cloneId);
            renderClonesTable(allClones);
        } else {
            alert("Ralat: " + res.message);
        }
    })
    .catch(err => {
        alert("Ralat sambungan pelayan. Gagal memadam sampel klon.");
    });
}
