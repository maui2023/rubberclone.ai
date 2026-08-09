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

    // 6. Logik Pengurusan Pekeliling & Makluman (Announcements Management)
    if (document.getElementById("announcements-data-table")) {
        initAnnouncements();
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

// // --- UPAH & STATE PAGINATION PENTADBIR ---
const paginationState = {
    users: { page: 1, limit: 10 },
    history: { page: 1, limit: 10 },
    clones: { page: 1, limit: 10 },
    announcements: { page: 1, limit: 10 }
};

function updateTablePagination(prefix, totalItems) {
    const state = paginationState[prefix];
    const totalPages = Math.max(1, Math.ceil(totalItems / state.limit));
    
    if (state.page > totalPages) state.page = totalPages;
    if (state.page < 1) state.page = 1;

    const startItem = totalItems === 0 ? 0 : (state.page - 1) * state.limit + 1;
    const endItem = Math.min(totalItems, state.page * state.limit);

    const infoEl = document.getElementById(`${prefix}-pagination-info`);
    if (infoEl) {
        infoEl.innerText = `Menunjukkan ${startItem} - ${endItem} daripada ${totalItems} rekod`;
    }

    const indicatorEl = document.getElementById(`${prefix}-page-indicator`);
    if (indicatorEl) {
        indicatorEl.innerText = `Halaman ${state.page} / ${totalPages}`;
    }

    const prevBtn = document.getElementById(`${prefix}-prev-page`);
    const nextBtn = document.getElementById(`${prefix}-next-page`);

    if (prevBtn) prevBtn.disabled = state.page <= 1;
    if (nextBtn) nextBtn.disabled = state.page >= totalPages;

    const startIndex = (state.page - 1) * state.limit;
    return {
        startIndex: startIndex,
        endIndex: startIndex + state.limit
    };
}

function bindPaginationEvents(prefix, renderCallback) {
    const pageSizeSelect = document.getElementById(`${prefix}-page-size`);
    const prevBtn = document.getElementById(`${prefix}-prev-page`);
    const nextBtn = document.getElementById(`${prefix}-next-page`);

    if (pageSizeSelect) {
        pageSizeSelect.addEventListener('change', function () {
            paginationState[prefix].limit = parseInt(this.value);
            paginationState[prefix].page = 1;
            renderCallback();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            if (paginationState[prefix].page > 1) {
                paginationState[prefix].page--;
                renderCallback();
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            paginationState[prefix].page++;
            renderCallback();
        });
    }
}

// --- 2. LOGIK DIREKTORI PENGGUNA ---
let allUsers = [];

function applyUsersFiltersAndRender() {
    const query = (document.getElementById('search-users-input')?.value || '').toLowerCase();
    const statusVal = document.getElementById('filter-user-status')?.value || '';
    const roleVal = document.getElementById('filter-user-role')?.value || '';

    const filtered = allUsers.filter(user => {
        const matchesSearch = 
            user.fullname.toLowerCase().includes(query) ||
            user.username.toLowerCase().includes(query) ||
            user.email.toLowerCase().includes(query) ||
            user.agency.toLowerCase().includes(query);

        const matchesStatus = statusVal === '' || user.status === statusVal;
        const matchesRole = roleVal === '' || user.role === roleVal;

        return matchesSearch && matchesStatus && matchesRole;
    });

    const sliceInfo = updateTablePagination('users', filtered.length);
    const pageSlice = filtered.slice(sliceInfo.startIndex, sliceInfo.endIndex);
    renderUsersTable(pageSlice);
}

function initUsersDirectory() {
    const tableBody = document.getElementById('users-table-body');
    const searchInput = document.getElementById('search-users-input');
    const statusFilter = document.getElementById('filter-user-status');
    const roleFilter = document.getElementById('filter-user-role');
    const modal = document.getElementById('add-user-modal');
    const btnAddUser = document.getElementById('btn-add-user');
    const btnCloseModal = document.getElementById('btn-close-modal');
    const addUserForm = document.getElementById('add-user-form');
    const errorDiv = document.getElementById('add-user-error');
    const btnSubmit = document.getElementById('btn-submit-add-user');
    const modalTitle = document.getElementById('user-modal-title');
    const modalDesc = document.getElementById('user-modal-desc');
    const editIdField = document.getElementById('edit-user-id');
    const passwordHelp = document.getElementById('reg-password-help');
    const avatarContainer = document.getElementById('avatar-preview-container');
    const avatarImg = document.getElementById('user-avatar-img');

    // Mengambil data senarai pengguna secara dinamik
    function fetchAndRenderUsers() {
        tableBody.innerHTML = `<tr><td colspan="8" class="table-loading-row"><span class="spinner"></span> Memuatkan rekod pendaftaran pengguna...</td></tr>`;
        fetch(getApiUrl('api/admin/users'))
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    allUsers = res.data;
                    paginationState.users.page = 1;
                    applyUsersFiltersAndRender();
                } else {
                    allUsers = [];
                    tableBody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:#ef4444;">${res.message}</td></tr>`;
                    updateTablePagination('users', 0);
                }
            })
            .catch(err => {
                allUsers = [];
                tableBody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:#ef4444;">Ralat sambungan pelayan. Anda masih boleh menambah pengguna secara demonstrasi dalam UI.</td></tr>`;
                updateTablePagination('users', 0);
            });
    }

    fetchAndRenderUsers();

    if (searchInput) searchInput.addEventListener('input', () => { paginationState.users.page = 1; applyUsersFiltersAndRender(); });
    if (statusFilter) statusFilter.addEventListener('change', () => { paginationState.users.page = 1; applyUsersFiltersAndRender(); });
    if (roleFilter) roleFilter.addEventListener('change', () => { paginationState.users.page = 1; applyUsersFiltersAndRender(); });

    bindPaginationEvents('users', applyUsersFiltersAndRender);

    // Urus Modal Tunjuk/Sembunyi (Tambah Pengguna)
    if (btnAddUser && modal) {
        btnAddUser.addEventListener('click', () => {
            modalTitle.innerText = 'Daftar Pengguna Baharu';
            modalDesc.innerText = 'Cipta akaun pegawai lapangan atau pentadbir RISDA secara selamat.';
            btnSubmit.innerText = 'Daftar Pengguna';
            editIdField.value = '';
            if (passwordHelp) passwordHelp.style.display = 'none';
            if (avatarContainer) avatarContainer.style.display = 'none';
            errorDiv.style.display = 'none';
            addUserForm.reset();
            modal.style.display = 'flex';
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

    // Urus Hantar Borang (Submit Form - Create or Update)
    if (addUserForm) {
        addUserForm.addEventListener('submit', function (e) {
            e.preventDefault();
            
            const editId = editIdField.value;
            const isEdit = editId !== '';

            const formData = {
                fullname: document.getElementById('reg-fullname').value,
                username: document.getElementById('reg-username').value,
                email: document.getElementById('reg-email').value,
                password: document.getElementById('reg-password').value,
                agency: document.getElementById('reg-agency').value,
                role: document.getElementById('reg-role').value,
                status: document.getElementById('reg-status').value
            };

            if (isEdit) {
                formData.user_id = parseInt(editId);
            }

            errorDiv.style.display = 'none';
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = isEdit ? 'Mengemaskini...' : 'Mendaftarkan...';

            const apiEndpoint = isEdit ? 'api/admin/update_user' : 'api/admin/create_user';

            fetch(getApiUrl(apiEndpoint), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(res => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = isEdit ? 'Kemas Kini Pengguna' : 'Daftar Pengguna';
                
                if (res.status === 'success') {
                    modal.style.display = 'none';
                    addUserForm.reset();
                    editIdField.value = '';
                    fetchAndRenderUsers();
                } else {
                    errorDiv.innerText = res.message || 'Ralat semasa memproses maklumat pengguna.';
                    errorDiv.style.display = 'block';
                }
            })
            .catch(err => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = isEdit ? 'Kemas Kini Pengguna' : 'Daftar Pengguna';
                errorDiv.innerText = 'Ralat sambungan pelayan. Gagal menyimpan data.';
                errorDiv.style.display = 'block';
            });
        });
    }
}

function renderUsersTable(usersList) {
    const tableBody = document.getElementById('users-table-body');
    
    if (usersList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding: 2rem;">Tiada pengguna ditemui.</td></tr>`;
        return;
    }

    tableBody.innerHTML = '';
    usersList.forEach(user => {
        const isChecked = user.status === 'active' ? 'checked' : '';
        const badgeClass = user.status === 'active' ? 'active' : 'inactive';
        const badgeLabel = user.status === 'active' ? 'Aktif' : 'Nyahaktif';
        const initial = (user.fullname || 'U').charAt(0).toUpperCase();

        const avatarHtml = user.avatar_url 
            ? `<img src="${user.avatar_url}" alt="${escapeHtml(user.fullname)}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1.5px solid var(--color-emerald); box-shadow: 0 0 8px var(--color-emerald-glow);">`
            : `<div class="admin-avatar" style="width: 36px; height: 36px; font-size: 0.85rem; border-radius: 50%; background: var(--color-emerald); color: var(--color-bg-dark); font-weight: 700; display: flex; align-items: center; justify-content: center;">${initial}</div>`;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${avatarHtml}</td>
            <td style="font-weight:600; color:var(--color-mint-light);">${escapeHtml(user.fullname)}</td>
            <td>@${escapeHtml(user.username)}</td>
            <td>${escapeHtml(user.email)}</td>
            <td>${escapeHtml(user.agency)}</td>
            <td style="text-align:center; font-weight:bold;">${user.total_scans}</td>
            <td>
                <span class="status-badge ${badgeClass}" id="badge-status-${user.id}">${badgeLabel}</span>
            </td>
            <td>
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <button class="btn-action-edit" title="Sunting Pengguna" onclick="editUser(${user.id})" style="padding: 0.4rem; border-radius: var(--radius-sm); color: var(--color-text-muted); display: inline-flex; align-items: center; justify-content: center; transition: var(--transition-smooth); border: 1px solid transparent; background: none; cursor: pointer;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <!-- Suis Togol Pintar -->
                    <label class="switch" aria-label="Tukar status akses pengguna">
                        <input type="checkbox" ${isChecked} onchange="toggleUserAccess(${user.id}, this)">
                        <span class="slider"></span>
                    </label>
                </div>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

// Membuka modal sunting pengguna dengan data sedia ada & paparan gambar profil
function editUser(userId) {
    const user = allUsers.find(u => u.id === userId);
    if (!user) return;

    const modal = document.getElementById('add-user-modal');
    const modalTitle = document.getElementById('user-modal-title');
    const modalDesc = document.getElementById('user-modal-desc');
    const btnSubmit = document.getElementById('btn-submit-add-user');
    const editIdField = document.getElementById('edit-user-id');
    const passwordHelp = document.getElementById('reg-password-help');
    const errorDiv = document.getElementById('add-user-error');
    const avatarContainer = document.getElementById('avatar-preview-container');
    const avatarImg = document.getElementById('user-avatar-img');

    modalTitle.innerText = `Kemaskini Profil Pengguna`;
    modalDesc.innerText = `Sunting maklumat pegawai atau pentadbir: ${user.fullname}`;
    btnSubmit.innerText = 'Kemas Kini Pengguna';
    editIdField.value = user.id;
    errorDiv.style.display = 'none';

    // Isi borang dengan data sedia ada
    document.getElementById('reg-fullname').value = user.fullname || '';
    document.getElementById('reg-username').value = user.username || '';
    document.getElementById('reg-email').value = user.email || '';
    document.getElementById('reg-agency').value = user.agency || '';
    document.getElementById('reg-role').value = user.role || 'user';
    document.getElementById('reg-status').value = user.status || 'active';
    document.getElementById('reg-password').value = '';

    if (passwordHelp) passwordHelp.style.display = 'block';

    // Paparkan gambar profil jika dimuat naik
    if (user.avatar_url && avatarContainer && avatarImg) {
        avatarImg.src = user.avatar_url;
        avatarContainer.style.display = 'flex';
    } else if (avatarContainer) {
        avatarContainer.style.display = 'none';
    }

    modal.style.display = 'flex';
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
            const u = allUsers.find(item => item.id === userId);
            if (u) u.status = newStatus;
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

function applyHistoryFiltersAndRender() {
    const query = (document.getElementById('search-history-input')?.value || '').toLowerCase();
    const selectedClone = document.getElementById('filter-clone-select')?.value || '';
    const selectedConfidence = document.getElementById('filter-confidence-select')?.value || '';

    const filtered = allRecords.filter(rec => {
        const matchesSearch = 
            (rec.fullname || '').toLowerCase().includes(query) ||
            (rec.username || '').toLowerCase().includes(query) ||
            (rec.location_name || '').toLowerCase().includes(query) ||
            (rec.clone_name || '').toLowerCase().includes(query);

        const matchesClone = selectedClone === "" || rec.clone_name === selectedClone;

        let matchesConfidence = true;
        if (selectedConfidence === 'high') {
            matchesConfidence = rec.confidence >= 0.90;
        } else if (selectedConfidence === 'medium') {
            matchesConfidence = rec.confidence >= 0.75 && rec.confidence < 0.90;
        } else if (selectedConfidence === 'low') {
            matchesConfidence = rec.confidence < 0.75;
        }

        return matchesSearch && matchesClone && matchesConfidence;
    });

    const sliceInfo = updateTablePagination('history', filtered.length);
    const pageSlice = filtered.slice(sliceInfo.startIndex, sliceInfo.endIndex);
    renderHistoryTable(pageSlice);
}

function initHistoryAudit() {
    const tableBody = document.getElementById('history-table-body');
    const searchInput = document.getElementById('search-history-input');
    const filterSelect = document.getElementById('filter-clone-select');
    const confidenceSelect = document.getElementById('filter-confidence-select');
    const modal = document.getElementById('info-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');

    // Mengambil data senarai imbasan daun
    fetch(getApiUrl('api/analysis/list?all=true'))
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                allRecords = res.data;
                paginationState.history.page = 1;
                applyHistoryFiltersAndRender();
            } else {
                tableBody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:#ef4444;">${res.message}</td></tr>`;
                updateTablePagination('history', 0);
            }
        })
        .catch(err => {
            tableBody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:#ef4444;">Ralat sambungan pelayan.</td></tr>`;
            updateTablePagination('history', 0);
        });

    // Carian sejarah imbasan
    if (searchInput) searchInput.addEventListener('input', () => { paginationState.history.page = 1; applyHistoryFiltersAndRender(); });
    if (filterSelect) filterSelect.addEventListener('change', () => { paginationState.history.page = 1; applyHistoryFiltersAndRender(); });
    if (confidenceSelect) confidenceSelect.addEventListener('change', () => { paginationState.history.page = 1; applyHistoryFiltersAndRender(); });

    bindPaginationEvents('history', applyHistoryFiltersAndRender);

    // Tutup modal
    if (closeModalBtn && modal) {
        closeModalBtn.addEventListener('click', () => modal.style.display = 'none');
        window.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });
    }
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
            <td style="font-weight:600; color:var(--color-mint-light);">${escapeHtml(rec.fullname || rec.username)}</td>
            <td style="color:var(--color-gold-latex); font-weight:600;">${escapeHtml(rec.clone_name)}</td>
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
                <strong>${escapeHtml(rec.location_name)}</strong><br>
                <span style="color:var(--color-text-muted);">${rec.latitude.toFixed(4)}, ${rec.longitude.toFixed(4)}</span>
            </td>
            <td style="font-size:0.8rem; max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${escapeHtml(rec.notes || '')}">
                ${escapeHtml(rec.notes) || '<span style="color:var(--color-text-muted);">Tiada catatan</span>'}
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
            <span class="modal-field-val" style="font-weight:600;">${escapeHtml(rec.fullname)} (@${escapeHtml(rec.username)})</span>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Agensi RISDA</span>
            <span class="modal-field-val">${escapeHtml(rec.agency)}</span>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Keputusan Analisis Klon</span>
            <span class="modal-field-val" style="color:var(--color-gold-latex); font-weight:700; font-size:1.1rem;">
                ${escapeHtml(rec.clone_name)} <span style="color:var(--color-emerald); font-size:0.9rem;">(${(rec.confidence * 100).toFixed(0)}% keyakinan)</span>
            </span>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Tarikh Imbasan</span>
            <span class="modal-field-val">${dateStr}</span>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Lokasi Stesen Tapak</span>
            <span class="modal-field-val">${escapeHtml(rec.location_name)} (${rec.latitude.toFixed(5)}, ${rec.longitude.toFixed(5)})</span>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Maklumat Tanah & Elevasi</span>
            <span class="modal-field-val">Tanah: ${escapeHtml(rec.soil_type)} | Taburan Hujan: ${escapeHtml(rec.rainfall)} | Elevasi: ${escapeHtml(rec.elevation)}</span>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Catatan AI Gemini</span>
            <span class="modal-field-val" style="font-style:italic; background:rgba(255,255,255,0.02); padding:0.75rem; border-radius:var(--radius-sm); border:1px solid rgba(255,255,255,0.04);">${escapeHtml(rec.notes) || 'Tiada catatan tambahan.'}</span>
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
            allRecords = allRecords.filter(item => item.id !== recordId);
            applyHistoryFiltersAndRender();
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
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

// --- 5. LOGIK PENGURUSAN SAMPEL KLON ---
let allClones = [];

function applyClonesFiltersAndRender() {
    const query = (document.getElementById('search-clones-input')?.value || '').toLowerCase();
    const latexVal = document.getElementById('filter-latex-select')?.value || '';
    const statusVal = document.getElementById('filter-clone-status-select')?.value || '';

    const filtered = allClones.filter(clone => {
        const matchesSearch = 
            clone.clone_name.toLowerCase().includes(query) ||
            (clone.warisan && clone.warisan.toLowerCase().includes(query)) ||
            (clone.bentuk_daun && clone.bentuk_daun.toLowerCase().includes(query)) ||
            (clone.warna_lateks && clone.warna_lateks.toLowerCase().includes(query));

        const matchesLatex = latexVal === '' || (clone.warna_lateks && clone.warna_lateks.toLowerCase().includes(latexVal.toLowerCase()));
        const matchesStatus = statusVal === '' || clone.status === statusVal;

        return matchesSearch && matchesLatex && matchesStatus;
    });

    const sliceInfo = updateTablePagination('clones', filtered.length);
    const pageSlice = filtered.slice(sliceInfo.startIndex, sliceInfo.endIndex);
    renderClonesTable(pageSlice);
}

function initCloneSamples() {
    const tableBody = document.getElementById('clones-table-body');
    const searchInput = document.getElementById('search-clones-input');
    const latexFilter = document.getElementById('filter-latex-select');
    const statusFilter = document.getElementById('filter-clone-status-select');
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
                    paginationState.clones.page = 1;
                    applyClonesFiltersAndRender();
                } else {
                    allClones = [];
                    tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:#ef4444;">${res.message}</td></tr>`;
                    updateTablePagination('clones', 0);
                }
            })
            .catch(err => {
                allClones = [];
                tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:#ef4444;">Ralat sambungan pelayan. Sila semak konfigurasi pangkalan data.</td></tr>`;
                updateTablePagination('clones', 0);
            });
    }

    fetchAndRenderClones();

    if (searchInput) searchInput.addEventListener('input', () => { paginationState.clones.page = 1; applyClonesFiltersAndRender(); });
    if (latexFilter) latexFilter.addEventListener('change', () => { paginationState.clones.page = 1; applyClonesFiltersAndRender(); });
    if (statusFilter) statusFilter.addEventListener('change', () => { paginationState.clones.page = 1; applyClonesFiltersAndRender(); });

    bindPaginationEvents('clones', applyClonesFiltersAndRender);

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
            applyClonesFiltersAndRender();
        } else {
            alert("Ralat: " + res.message);
        }
    })
    .catch(err => {
        alert("Ralat sambungan pelayan. Gagal memadam sampel klon.");
    });
}

// --- 6. LOGIK PENGURUSAN PEKELILING & MAKLUMAN ---
let allAnnouncements = [];

function applyAnnouncementsFiltersAndRender() {
    const query = (document.getElementById('search-announcements-input')?.value || '').toLowerCase();
    const statusVal = document.getElementById('filter-announcement-status')?.value || '';

    const filtered = allAnnouncements.filter(item => {
        const matchesSearch = 
            item.title.toLowerCase().includes(query) ||
            (item.content && item.content.toLowerCase().includes(query)) ||
            (item.author && item.author.toLowerCase().includes(query));

        const matchesStatus = statusVal === '' || item.status === statusVal;

        return matchesSearch && matchesStatus;
    });

    const sliceInfo = updateTablePagination('announcements', filtered.length);
    const pageSlice = filtered.slice(sliceInfo.startIndex, sliceInfo.endIndex);
    renderAnnouncementsTable(pageSlice);
}

function initAnnouncements() {
    const tableBody = document.getElementById('announcements-table-body');
    const searchInput = document.getElementById('search-announcements-input');
    const statusFilter = document.getElementById('filter-announcement-status');
    const modal = document.getElementById('announcement-modal');
    const detailModal = document.getElementById('announcement-detail-modal');
    const btnAddAnnouncement = document.getElementById('btn-add-announcement');
    const btnCloseModal = document.getElementById('btn-close-announcement-modal');
    const closeDetailBtn = document.getElementById('close-announcement-detail-btn');
    const announcementForm = document.getElementById('announcement-form');
    const errorDiv = document.getElementById('announcement-form-error');
    const btnSubmit = document.getElementById('btn-submit-announcement');
    const modalTitle = document.getElementById('announcement-modal-title');
    const editIdField = document.getElementById('announcement-edit-id');

    function fetchAndRenderAnnouncements() {
        tableBody.innerHTML = `<tr><td colspan="6" class="table-loading-row"><span class="spinner"></span> Memuatkan senarai pekeliling & makluman...</td></tr>`;
        fetch(getApiUrl('api/announcements/list_all'))
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success') {
                    allAnnouncements = res.data;
                    paginationState.announcements.page = 1;
                    applyAnnouncementsFiltersAndRender();
                } else {
                    allAnnouncements = [];
                    tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#ef4444;">${res.message}</td></tr>`;
                    updateTablePagination('announcements', 0);
                }
            })
            .catch(err => {
                allAnnouncements = [];
                tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#ef4444;">Ralat sambungan pelayan. Sila semak sambungan pangkalan data.</td></tr>`;
                updateTablePagination('announcements', 0);
            });
    }

    fetchAndRenderAnnouncements();

    if (searchInput) searchInput.addEventListener('input', () => { paginationState.announcements.page = 1; applyAnnouncementsFiltersAndRender(); });
    if (statusFilter) statusFilter.addEventListener('change', () => { paginationState.announcements.page = 1; applyAnnouncementsFiltersAndRender(); });

    bindPaginationEvents('announcements', applyAnnouncementsFiltersAndRender);

    // Modal Tambah Pekeliling
    if (btnAddAnnouncement && modal) {
        btnAddAnnouncement.addEventListener('click', () => {
            modalTitle.innerText = 'Tambah Pekeliling Baharu';
            btnSubmit.innerText = 'Terbitkan Pekeliling';
            editIdField.value = '';
            errorDiv.style.display = 'none';
            announcementForm.reset();
            modal.style.display = 'flex';
        });
    }

    if (btnCloseModal && modal) {
        btnCloseModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });
    }

    if (closeDetailBtn && detailModal) {
        closeDetailBtn.addEventListener('click', () => detailModal.style.display = 'none');
        detailModal.addEventListener('click', (e) => {
            if (e.target === detailModal) detailModal.style.display = 'none';
        });
    }

    // Submit form (Create / Update)
    if (announcementForm) {
        announcementForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const editId = editIdField.value;
            const isEdit = editId !== '';

            const publishDateVal = document.getElementById('announcement-publish-date').value;
            const expireDateVal = document.getElementById('announcement-expire-date').value;

            const publishAtMs = publishDateVal ? new Date(publishDateVal).getTime() : Date.now();
            const expiresAtMs = expireDateVal ? new Date(expireDateVal).getTime() : null;

            const formData = {
                title: document.getElementById('announcement-title').value,
                content: document.getElementById('announcement-content').value,
                publish_at: publishAtMs,
                expires_at: expiresAtMs,
                status: document.getElementById('announcement-status').value
            };

            if (isEdit) {
                formData.id = parseInt(editId);
            }

            errorDiv.style.display = 'none';
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = isEdit ? 'Mengemaskini...' : 'Menerbitkan...';

            const apiPath = isEdit ? 'api/announcements/update' : 'api/announcements/create';

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
                btnSubmit.innerHTML = isEdit ? 'Kemas Kini Pekeliling' : 'Terbitkan Pekeliling';

                if (res.status === 'success') {
                    modal.style.display = 'none';
                    announcementForm.reset();
                    editIdField.value = '';
                    fetchAndRenderAnnouncements();
                } else {
                    errorDiv.innerText = res.message || 'Ralat semasa memproses pekeliling.';
                    errorDiv.style.display = 'block';
                }
            })
            .catch(err => {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = isEdit ? 'Kemas Kini Pekeliling' : 'Terbitkan Pekeliling';
                errorDiv.innerText = 'Ralat sambungan pelayan. Gagal menyimpan pekeliling.';
                errorDiv.style.display = 'block';
            });
        });
    }
}

function renderAnnouncementsTable(list) {
    const tableBody = document.getElementById('announcements-table-body');

    if (list.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 2rem;">Tiada pekeliling atau makluman ditemui.</td></tr>`;
        return;
    }

    tableBody.innerHTML = '';
    list.forEach(item => {
        const publishStr = item.publish_at ? new Date(item.publish_at).toLocaleDateString('ms-MY') : '-';
        const expireStr = item.expires_at ? new Date(item.expires_at).toLocaleDateString('ms-MY') : 'Tiada';
        const badgeClass = item.status === 'active' ? 'active' : 'inactive';
        const badgeLabel = item.status === 'active' ? 'Aktif' : 'Draf/Nyahaktif';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="font-weight:600; color:var(--color-mint-light); cursor:pointer;" onclick="viewAnnouncementDetail(${item.id})" title="Klik untuk lihat kandungan penuh">${escapeHtml(item.title)}</td>
            <td style="font-size:0.85rem;">${escapeHtml(item.author || 'Pentadbir')}</td>
            <td style="font-size:0.85rem;">${publishStr}</td>
            <td style="font-size:0.85rem;">${expireStr}</td>
            <td>
                <span class="status-badge ${badgeClass}">${badgeLabel}</span>
            </td>
            <td>
                <div style="display:flex; gap:0.5rem;">
                    <button class="btn-action-edit" title="Sunting Pekeliling" onclick="editAnnouncement(${item.id})" style="padding: 0.4rem; border-radius: var(--radius-sm); color: var(--color-text-muted); display: inline-flex; align-items: center; justify-content: center; transition: var(--transition-smooth); border: 1px solid transparent; background: none; cursor: pointer;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button class="btn-action-delete" title="Padam Pekeliling" onclick="deleteAnnouncement(${item.id}, '${escapeHtml(item.title)}')">
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

function viewAnnouncementDetail(id) {
    const item = allAnnouncements.find(a => a.id === id);
    if (!item) return;

    const modal = document.getElementById('announcement-detail-modal');
    const titleEl = document.getElementById('detail-announcement-title');
    const bodyEl = document.getElementById('detail-announcement-body');

    titleEl.innerText = item.title;

    const publishStr = item.publish_at ? new Date(item.publish_at).toLocaleString('ms-MY') : '-';
    const expireStr = item.expires_at ? new Date(item.expires_at).toLocaleString('ms-MY') : 'Tiada';

    bodyEl.innerHTML = `
        <div class="modal-field">
            <span class="modal-field-label">Pengarang</span>
            <span class="modal-field-val" style="font-weight:600;">${escapeHtml(item.author || 'RISDA Pentadbir')}</span>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Tarikh Siaran & Luput</span>
            <span class="modal-field-val">${publishStr} &rarr; ${expireStr}</span>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Status</span>
            <span class="modal-field-val"><span class="status-badge ${item.status === 'active' ? 'active' : 'inactive'}">${item.status === 'active' ? 'Aktif' : 'Draf/Nyahaktif'}</span></span>
        </div>
        <div class="modal-field" style="margin-top: 1rem;">
            <span class="modal-field-label">Kandungan Pekeliling</span>
            <div class="modal-field-val" style="white-space: pre-line; background: rgba(255,255,255,0.02); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.04); margin-top: 0.5rem; line-height: 1.6;">${escapeHtml(item.content)}</div>
        </div>
    `;

    modal.style.display = 'flex';
}

function editAnnouncement(id) {
    const item = allAnnouncements.find(a => a.id === id);
    if (!item) return;

    const modal = document.getElementById('announcement-modal');
    const modalTitle = document.getElementById('announcement-modal-title');
    const btnSubmit = document.getElementById('btn-submit-announcement');
    const editIdField = document.getElementById('announcement-edit-id');
    const errorDiv = document.getElementById('announcement-form-error');

    modalTitle.innerText = `Sunting Pekeliling`;
    btnSubmit.innerText = 'Kemas Kini Pekeliling';
    editIdField.value = item.id;
    errorDiv.style.display = 'none';

    document.getElementById('announcement-title').value = item.title || '';
    document.getElementById('announcement-content').value = item.content || '';
    document.getElementById('announcement-status').value = item.status || 'active';

    if (item.publish_at) {
        const pDate = new Date(item.publish_at);
        document.getElementById('announcement-publish-date').value = pDate.toISOString().split('T')[0];
    } else {
        document.getElementById('announcement-publish-date').value = '';
    }

    if (item.expires_at) {
        const eDate = new Date(item.expires_at);
        document.getElementById('announcement-expire-date').value = eDate.toISOString().split('T')[0];
    } else {
        document.getElementById('announcement-expire-date').value = '';
    }

    modal.style.display = 'flex';
}

function deleteAnnouncement(id, title) {
    if (!confirm(`Adakah anda pasti mahu memadamkan pekeliling "${title}"? Tindakan ini tidak boleh ditarik balik.`)) {
        return;
    }

    fetch(getApiUrl(`api/announcements/delete?id=${id}`), {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            allAnnouncements = allAnnouncements.filter(a => a.id !== id);
            applyAnnouncementsFiltersAndRender();
        } else {
            alert("Ralat: " + res.message);
        }
    })
    .catch(err => {
        alert("Ralat sambungan pelayan. Gagal memadam pekeliling.");
    });
}
