# 🌟 Smart NGO Platform - Frontend Interactive Demo

This is a **standalone demo** version designed to showcase the platform's UI and functionality without requiring a local server (PHP/MySQL).

## 🚀 How to Run
Simply navigate into the `v2-frontend-demo` folder and **double-click `index.html`** to open it in your browser.

## 💡 Demo Features
- **Project Browsing**: View projects and their transparency timelines.
- **Interactive Donations**: Click the "Donate" button on project details to see the progress bar and timeline update in real-time.
- **Mock Dashboards**: Login with any email to see the Donor/NGO/Admin views (Email matching `ngo` or `admin` triggers specific roles).
- **Persistent State**: The demo uses your browser's `localStorage` to remember your donations and project progress.

## 🛠️ Testing Scenarios
1. **Donor Flow**: Open a project -> Click "Demo Donate $100" -> See the progress bar increase and state persist.
2. **NGO Flow**: Login with `ngo@demo.com` -> Access the NGO dashboard.
3. **Resetting**: If you want to start over, click the **"Reset Demo Data"** button at the bottom of the Dashboard.

*Note: For the full production-ready version with a database, please refer to the main README.md in the root directory.*
