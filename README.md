# GrandMaster ERP — Multi-Company ERP System (Vannila PHP)

GrandMaster ERP is a fully custom-built enterprise resource planning system designed to support multiple business divisions under a single centralized platform.  
This repository contains a sanitized version of the frontend/backend structure and documentation.  
All sensitive configuration files, uploads, and proprietary client data have been removed.

---

## 🚀 Project Overview

GrandMaster ERP supports two different business divisions:

1. **Spare Parts (Trading)**
2. **IT Services / Consultation**

Each division operates with the same ERP framework but has its own workflows and modules.

The system was built in **Vannila PHP** with AJAX-based interactions, modular folder structure, and a custom RBAC (Role-Based Access Control) engine.

---

# 🏗 Key Modules & Features

## 🧾 1. Quotation → Approval → Sales Order Process
- Multi-level internal approval workflow  
- Email notification with customer acceptance link  
- Customer PO attachment & re-approval workflow  
- Direct conversion from Quotation → Sales Order  

---

## 🏬 2. Warehouse & Supply Chain
### **Spare Parts Division**
- Warehouse processing workflow  
- Pick & pack slip generation  
- Goods Delivery Note (signed by customer)  
- Inventory sync  
- Supplier onboarding  
- Request for Proposal (RFP)  
- Supplier quotation handling  
- Purchase Order creation  
- Goods receipt + supplier payment logging  

---

## 💼 3. Projects & SMA (IT Consultation Division)
- Auto-create projects from Sales Orders  
- Task assignment & user-level responsibilities  
- Project head & member assignments  
- SMA (Service Maintenance Agreement) creation  
- Proposal templates & dynamic SOW (Scope of Work) generation  

---

## 🔐 4. Role-Based Access Control (RBAC)
- Role definitions with fine-grained capabilities  
- Per-user capability overrides  
- Module-wise permissions (quotation, SO, PO, customers, etc.)  
- Restrict edit/delete actions based on defined privileges  

---

## 💰 5. Automated Accounting Integration
Accounting entries are automatically generated for:

- Customer invoice creation  
- Customer payments  
- Supplier purchase payments  
- Journal recognition  
- Multi-company accounting segregation  

(Manual accounting code is removed in this sanitized version.)

---

## 📦 Folder Structure (Sanitized)

grandmastererp/
├─ ajax/
│ ├─ customers/
│ ├─ suppliers/
│ ├─ documents/
│ └─ common handlers
├─ modules/
├─ includes/
│ ├─ globals.php
│ ├─ helper.php
│ ├─ sessions.php
│ └─ * masterconfig.php (ignored)
├─ assets/
├─ migrations/ (empty)
├─ seeders/ (empty)
├─ uploads/ (ignored entirely)
└─ dashboard.php

---

## 👨‍💻 My Responsibilities

- Designed end-to-end architecture for the ERP system  
- Built Quotation → Approval → Sales Order → Delivery → Invoice automation  
- Developed warehouse & supply chain modules  
- Implemented project & task management for IT division  
- Designed RBAC with user-level overrides  
- Integrated auto-accounting entries for financial workflows  
- Implemented email flows & customer acceptance mechanisms  
- Created migration & seeding system for local deployments  

---

## ⚠️ Disclaimer

This repository contains **sanitized and non-sensitive files only**.  
Production credentials, customer data, and attachments have been removed for security.

