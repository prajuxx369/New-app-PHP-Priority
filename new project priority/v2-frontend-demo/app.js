// High-Fidelity Payment Simulator for Smart NGO
const DEFAULT_PROJECTS = [
    {
        id: 1,
        title: "Clean Water for Rural Schools",
        ngo: "AquaHope International",
        category: "Health",
        goal: 15000,
        raised: 12450,
        image: "images/ng_project_education_jpg_1775576481124.png",
        description: "Installing advanced multi-stage water filtration systems in over 20 schools. Providing safe drinking water to 5,000+ students.",
        updates: [
            { date: "2026-03-20", title: "Project Inception", text: "Site surveys completed for all 20 locations.", amount: 2000 },
            { date: "2026-04-01", title: "Equipment Procured", text: "50 high-grade filters and piping systems delivered.", amount: 8000 }
        ]
    },
    {
        id: 2,
        title: "Green Canopy: Urban Reforestation",
        ngo: "EcoGuardians",
        category: "Environment",
        goal: 25000,
        raised: 18700,
        image: "images/ngo_project_forest_jpg_1775576501004.png",
        description: "Restoring the urban ecosystem by planting 10,000 native trees in metropolitan lung spaces. Improving air quality and urban cooling.",
        updates: [
            { date: "2026-03-15", title: "Sapling Nursery Setup", text: "10k healthy saplings prepared for planting.", amount: 5000 }
        ]
    },
    {
        id: 3,
        title: "Mobile Health Clinic for Remotes",
        ngo: "HealthVanguard Network",
        category: "Health",
        goal: 50000,
        raised: 31200,
        image: "images/ngo_project_medical_jpg_1775576520339.png",
        description: "Equipping a state-of-the-art mobile medical van to reach areas with no hospitals. Providing primary care and vaccinations.",
        updates: [
            { date: "2026-03-25", title: "Van Customization", text: "Specialized medical equipment installed in the vehicle.", amount: 12000 }
        ]
    },
    {
        id: 4,
        title: "Code-A-Future: Tech for Orphans",
        ngo: "FutureFoundry",
        category: "Education",
        goal: 10000,
        raised: 4500,
        image: "images/ng_project_education_jpg_1775576481124.png",
        description: "Setting up a computer lab in City Orphanage. Providing weekly coding and digital literacy workshops to underprivileged youth.",
        updates: []
    }
];

function getProjects() {
    let projs = JSON.parse(localStorage.getItem('ngo_projects'));
    if (!projs) {
        localStorage.setItem('ngo_projects', JSON.stringify(DEFAULT_PROJECTS));
        return DEFAULT_PROJECTS;
    }
    return projs;
}

function initiateSecureDonation(id) {
    const project = getProjects().find(p => p.id === id);
    const modalHtml = `
        <div id="paymentModal" class="payment-modal" style="display: flex;">
            <div class="payment-card animate-up">
                <div class="gateway-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold m-0"><i class="fas fa-lock text-success me-2"></i>Secure Checkout</h4>
                        <p class="text-muted small m-0">Transaction ID: #NGO-${Math.floor(Math.random()*1000000)}</p>
                    </div>
                    <button onclick="closePaymentModal()" class="btn-close"></button>
                </div>
                
                <div id="paymentInitial" class="p-4">
                    <div class="mb-4">
                        <label class="small fw-bold text-uppercase text-muted mb-2">Supporting</label>
                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                            <img src="${project.image}" class="rounded-2 me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div><h6 class="fw-bold m-0">${project.title}</h6><p class="small text-muted m-0">${project.ngo}</p></div>
                        </div>
                    </div>

                    <div class="card-input-group">
                        <div class="card-field-box">
                            <label class="small text-muted d-block">Card Number</label>
                            <div class="d-flex align-items-center">
                                <input type="text" id="cardNumber" placeholder="4242 4242 4242 4242" class="border-0 w-100 outline-none" style="outline:none;" oninput="formatCard(this)">
                                <i class="fab fa-cc-visa fs-4 text-primary"></i>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="card-field-box">
                                    <label class="small text-muted d-block">Expiry</label>
                                    <input type="text" id="cardExpiry" placeholder="MM / YY" class="border-0 w-100 outline-none" style="outline:none;" oninput="formatExpiry(this)">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card-field-box">
                                    <label class="small text-muted d-block">CVC</label>
                                    <input type="password" id="cardCVC" placeholder="•••" maxlength="3" class="border-0 w-100 outline-none" style="outline:none;">
                                </div>
                            </div>
                        </div>
                        <div class="card-field-box">
                            <label class="small text-muted d-block">Cardholder Name</label>
                            <input type="text" id="cardName" value="${JSON.parse(localStorage.getItem('currentUser'))?.name || 'Nandini Kawdi'}" class="border-0 w-100 outline-none" style="outline:none;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                        <span class="text-muted">Donation Amount</span>
                        <span class="h4 fw-bold m-0 text-primary">$100.00</span>
                    </div>

                    <button onclick="startProcessing(${id})" class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-bold text-white shadow">Authorize Payment</button>
                    <p class="text-center mt-3 small text-muted"><i class="fas fa-shield-alt me-1"></i> Powering 100% Transparency Loop</p>
                </div>

                <div id="paymentProcessing" class="p-5 text-center d-none">
                    <div class="spinner-border text-primary mb-4" style="width: 3rem; height: 3rem;" role="status"></div>
                    <h4 class="fw-bold">Contacting Bank...</h4>
                    <p class="text-muted">Please do not refresh or close this window.</p>
                </div>

                <div id="payment3DS" class="p-5 text-center d-none">
                    <div class="mb-4"><i class="fas fa-university fs-1 text-primary"></i></div>
                    <h4 class="fw-bold mb-2">3D Secure Verification</h4>
                    <p class="text-muted small mb-4">We've sent a 6-digit code to your registered mobile number for added security.</p>
                    <input type="text" id="otpCode" placeholder="Enter OTP" class="form-control form-control-lg text-center fw-bold mb-4 rounded-3 letter-spacing-lg" maxlength="6">
                    <button onclick="finalizeTransaction(${id})" class="btn btn-primary w-100 py-3 rounded-pill text-white fw-bold">Verify & Pay</button>
                </div>

                <div id="paymentSuccess" class="p-5 text-center d-none">
                    <div class="success-checkmark mb-4">
                        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                            <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                            <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                        </svg>
                    </div>
                    <h3 class="fw-bold text-success mb-2">Impact Unlocked!</h3>
                    <p class="text-muted mb-4 px-3">Your contribution of <strong class="text-dark">$100</strong> has been successfully authorized and allocated to <strong>${project.title}</strong>.</p>
                    <div class="bg-light p-3 rounded-4 mb-4 text-start">
                        <div class="d-flex justify-content-between small text-muted mb-1"><span>Status:</span><span class="badge bg-success-subtle text-success">Verified</span></div>
                        <div class="d-flex justify-content-between small text-muted"><span>Receipt:</span><span class="fw-bold">#REC-${Math.floor(Math.random()*1000000)}</span></div>
                    </div>
                    <button onclick="location.reload()" class="btn btn-primary rounded-pill px-5 text-white fw-bold">Return to Platform</button>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function formatCard(input) {
    let v = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let matches = v.match(/\d{4,16}/g);
    let match = matches && matches[0] || '';
    let parts = [];
    for (i=0, len=match.length; i<len; i+=4) { parts.push(match.substring(i, i+4)); }
    if (parts.length) { input.value = parts.join(' '); } else { input.value = v; }
}

function formatExpiry(input) {
    let v = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    if (v.length >= 2) { input.value = v.substring(0,2) + ' / ' + v.substring(2,4); }
    else { input.value = v; }
}

function startProcessing(id) {
    document.getElementById('paymentInitial').classList.add('d-none');
    document.getElementById('paymentProcessing').classList.remove('d-none');
    setTimeout(() => {
        document.getElementById('paymentProcessing').classList.add('d-none');
        document.getElementById('payment3DS').classList.remove('d-none');
    }, 2000);
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) modal.remove();
}

function finalizeTransaction(id) {
    document.getElementById('payment3DS').classList.add('d-none');
    document.getElementById('paymentProcessing').classList.remove('d-none');
    document.getElementById('paymentProcessing').querySelector('h4').innerText = "Finalizing impact...";
    
    setTimeout(() => {
        document.getElementById('paymentProcessing').classList.add('d-none');
        document.getElementById('paymentSuccess').classList.remove('d-none');
        
        const projects = getProjects();
        const pIndex = projects.findIndex(p => p.id === id);
        projects[pIndex].raised += 100;
        localStorage.setItem('ngo_projects', JSON.stringify(projects));
        
        // Add to donation history if user logged in
        const user = JSON.parse(localStorage.getItem('currentUser'));
        if (user) {
            const history = JSON.parse(localStorage.getItem('donations_'+user.id)) || [];
            history.push({
                id: Date.now(),
                project: projects[pIndex].title,
                amount: 100,
                date: new Date().toISOString().split('T')[0],
                status: 'Verified'
            });
            localStorage.setItem('donations_'+user.id, JSON.stringify(history));
        }
    }, 2000);
}

function updateNav() {
    const user = JSON.parse(localStorage.getItem('currentUser'));
    const navActions = document.getElementById('navActions');
    if (user && navActions) {
        navActions.innerHTML = `
            <span class="me-3 small text-muted">Hi, ${user.name}</span>
            <a href="dashboard.html" class="btn btn-outline-primary me-2 rounded-pill px-4">Dashboard</a>
            <button onclick="logout()" class="btn btn-danger rounded-pill px-4 text-white">Logout</button>
        `;
    }
}

function logout() {
    localStorage.removeItem('currentUser');
    window.location.href = 'index.html';
}

function renderFeaturedProjects() {
    const container = document.getElementById('featuredProjects');
    if (!container) return;
    const projects = getProjects();
    container.innerHTML = projects.map(p => {
        const percent = Math.min(100, Math.round((p.raised/p.goal)*100));
        return `
            <div class="col-md-4 mb-4" data-aos="fade-up">
                <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <img src="${p.image}" class="card-img-top" style="height:200px; object-fit:cover;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3">${p.category || 'General'}</span>
                            <span class="small text-muted"><i class="fas fa-check-circle text-success me-1"></i>Vetted</span>
                        </div>
                        <h5 class="fw-bold mb-3">${p.title}</h5>
                        <p class="text-muted small mb-4" style="height: 3em; overflow: hidden;">${p.description}</p>
                        <div class="d-flex justify-content-between small mb-1"><strong>$${p.raised.toLocaleString()}</strong><span class="text-muted">$${p.goal.toLocaleString()} goal</span></div>
                        <div class="progress mb-4" style="height: 8px;"><div class="progress-bar bg-primary" style="width: ${percent}%"></div></div>
                        <a href="details.html?id=${p.id}" class="btn btn-outline-primary w-100 rounded-pill py-2">View Project</a>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}
