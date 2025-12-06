<div class="app-brand demo">
    <a href="/" class="app-brand-link">
        <!--        <img src="/assets/img/gmmsa_logo.png" alt="GrandMaster" width="190">-->
        <img src="/assets/img/gmm-g-logo.png" alt="GrandMaster" width="35">
        <span class="app-brand-text demo menu-text fw-bold">GrandMaster</span> </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
        <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i> </a>
</div>

<div class="menu-inner-shadow"></div>
<div class="companySelectionDiv">
    <div class="col-md-12 mb-6">
        <label for="companySelectionDropDown" class="form-label">Company</label>
        <select id="companySelectionDropDown" class="selectpicker w-100" data-style="btn-default">
            <?php
            $userCompanies = $authenticatedUser->getUserCompanies();
            foreach ($userCompanies as $company) {
                $selected = "";
                if ($_SESSION[SES_SELECTED_COMPANY] == $company['companyId'])
                    $selected = "selected";
                ?>
                <option value="<?= $company['companyId'] ?>" <?= $selected ?>><?= $company['companyLabel'] ?></option>
                <?php
            }
            ?>
        </select>
    </div>
</div>
<?php
//Spare parts
if ($_SESSION[SES_SELECTED_COMPANY] == 1) {
    ?>
    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item <?php if ($PAGE_ID == 'DASHBOARD') echo 'active' ?>">
            <a href="/dashboard" class="menu-link"> <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        <!-- HR Payroll -->
        <!-- <li class="menu-item  <?php if (str_starts_with($PAGE_ID, 'USER_') || strpos($PAGE_ID, 'COMPANY_ASSETS') === 0 || strpos($PAGE_ID, 'HR_SALARY_PAYROLL') === 0 || strpos($PAGE_ID, 'HR_ATTENDANCE') === 0) echo 'active open'; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-clipboard-list"></i>
                <div data-i18n="HR Workforce">HR Workforce</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if (str_starts_with($PAGE_ID, 'USER_')) echo 'active'; ?>">
                    <a href="/users/list" class="menu-link">
                        <div data-i18n="Employees">Employees</div>
                    </a>
                </li>
                <li class="menu-item  <?php if ($PAGE_ID == 'HR_ATTENDANCE') echo 'active' ?>">
                    <a href="/attendance" class="menu-link">
                        <div data-i18n="Attendance">Attendance</div>
                    </a>
                </li>
                <li class="menu-item  <?php if ($PAGE_ID == 'HR_SALARY_PAYROLL') echo 'active' ?>">
                    <a href="/payroll" class="menu-link">
                        <div data-i18n="Payroll">Payroll</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID === 'COMPANY_ASSETS') echo 'active'; ?>">
                    <a href="/companyassets/list" class="menu-link">
                        <div data-i18n="Company Assets">Company Assets</div>
                    </a>
                </li>
            </ul>
        </li> -->
        <!-- Chat -->
        <li class="menu-item <?php if ($PAGE_ID == 'CHAT') echo 'active' ?>">
          <a href="/chat" class="menu-link"> <i class="menu-icon tf-icons ti ti-message-2"></i>
            <div data-i18n="Chat">Chat</div>
          </a>
        </li>
        <!--CUSTOMERS-->
        <?php if (is_ceo() || has_permission('customers', 'view')) { ?>
        <li class="menu-item  <?php if (strpos($PAGE_ID, 'CUSTOMER_') === 0) echo 'active open'; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-building"></i>
                <div data-i18n="Customers">Customers</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item  <?php if ($PAGE_ID == 'CUSTOMER_LIST') echo 'active' ?>">
                    <a href="/customers/list" class="menu-link"><i class="menu-icon tf-icons ti ti-building"></i>
                        <div data-i18n="All Customers">All Customers</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == 'CUSTOMER_NEW') echo 'active' ?>">
                    <a href="/customers/new" class="menu-link"><i class="menu-icon tf-icons ti ti-building"></i>
                        <div data-i18n="Add Customer">Add Customer</div>
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>
        <!-- Quotations -->
         <?php if (is_ceo() || has_permission('quotations', 'view')) { ?>
        <li class="menu-item  <?php if (str_starts_with($PAGE_ID, 'QUOTATION_')) echo 'active open' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-file-text"></i>
                <div data-i18n="Quotation">Quotation</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item  <?php if ($PAGE_ID == 'QUOTATION_LIST' and !isset($_GET['filter'])) echo 'active' ?>">
                    <a href="/quotation/list" class="menu-link">
                        <div data-i18n="All Quotations">All Quotations</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == 'QUOTATION_LIST' and (isset($_GET['filter']) && $_GET['filter'] === 'DRAFT')) echo 'active' ?> ">
                    <a href="/quotation/list?filter=DRAFT" class="menu-link">
                        <div data-i18n="Drafts">Drafts</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == 'QUOTATION_LIST' and (isset($_GET['filter']) && $_GET['filter'] === 'ACTIVE')) echo 'active' ?> ">
                    <a href="/quotation/list?filter=ACTIVE" class="menu-link">
                        <div data-i18n="Active">Active</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == 'QUOTATION_LIST' and (isset($_GET['filter']) && $_GET['filter'] === 'CONFIRMED')) echo 'active' ?> ">
                    <a href="/quotation/list?filter=CONFIRMED" class="menu-link">
                        <div data-i18n="Confirmed">Confirmed</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/quotation/list" class="menu-link">
                        <div data-i18n="Quotation Reports">Quotation Reports</div>
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>
        <!-- Sales -->
        <li class="menu-item <?php if ((str_starts_with($PAGE_ID, 'SALESORDER_')) || (str_starts_with($PAGE_ID, 'ECOMMERCE_'))) echo 'active open' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
                <div data-i18n="Sales">Sales</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if ($PAGE_ID == 'SALESORDER_LIST' || $PAGE_ID == 'SALESORDER_EDIT') echo 'active' ?>">
                    <a href="/salesorders/list" class="menu-link">
                        <div data-i18n="Active Sales Order">Active Sales Order</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == 'ECOMMERCE_ORDER_LIST' || $PAGE_ID == 'ECOMMERCE_ORDER_EDIT') echo 'active' ?>">
                    <a href="/ecommerce/list" class="menu-link">
                        <div data-i18n="E-commerce Orders">E-commerce Orders</div>
                    </a>
                </li>
                <!-- <li class="menu-item">
                    <a href="../front-pages/pricing-page.html" class="menu-link" target="_blank">
                        <div data-i18n="Sales Reports">Sales Reports</div>
                    </a>
                </li> -->
            </ul>
        </li>

        <!-- Invoice -->
        <li class="menu-item <?php if (str_starts_with($PAGE_ID, 'INVOICE_')) echo 'active open' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-receipt"></i>
                <div data-i18n="Invoice">Invoice</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if ($PAGE_ID == 'INVOICE_LIST' || $PAGE_ID == 'INVOICE_EDIT') echo 'active' ?>">
                    <a href="/invoice/list" class="menu-link">
                        <div data-i18n="All Invoices">All Invoices</div>
                    </a>
                </li>
            </ul>
        </li>
        <!--Items/Products-->
        <?php if (is_ceo() || has_permission('itemsOrProducts', 'view')) { ?>
        <li class="menu-item <?php if (strpos($PAGE_ID, 'SPARE') === 0 || strpos($PAGE_ID, 'MACHINE') === 0 || strpos($PAGE_ID, 'CATEGORIES_') === 0 || strpos($PAGE_ID, 'SERVICES_') === 0 ) echo 'active open'; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle"> <i class="menu-icon tf-icons ti ti-tool"></i>
                <div data-i18n="Items/Products">Items/Products</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="/under-development" class="menu-link" >
                        <div data-i18n="Spare Parts Direct Sales">Spare Parts Direct Sales</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == SPARE_PART_LIST || $PAGE_ID == SPARE_PART_VIEW) echo 'active' ?>">
                    <a href="/spareparts/list" class="menu-link" target="">
                        <div data-i18n="Spare Parts">Spare Parts</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == 'MACHINE_LIST' || $PAGE_ID == 'MACHINE_VIEW') echo 'active' ?>">
                    <a href="/machines/list" class="menu-link" target="">
                        <div data-i18n="Machines">Machines</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/brands/list" class="menu-link">
                        <div data-i18n="Brands">Brands</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == 'SERVICES_LIST') echo 'active' ?>">
                    <a href="/services/list" class="menu-link" target="">
                        <div data-i18n="Services">Services</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == 'CATEGORIES_LIST') echo 'active' ?>">
                    <a href="/categories/list" class="menu-link" target="">
                        <div data-i18n="Categories">Categories</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/under-development" class="menu-link">
                        <div data-i18n="Spare Parts Reports">Spare Parts Reports</div>
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>  

        <li class="menu-item" style="display: none">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-file-invoice"></i>
                <div data-i18n="Purchase Order">Purchase Order</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="../front-pages/landing-page.html" class="menu-link" target="_blank">
                        <div data-i18n="New PO">New PO</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="../front-pages/pricing-page.html" class="menu-link" target="_blank">
                        <div data-i18n="Active PO">Active PO</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="../front-pages/pricing-page.html" class="menu-link" target="_blank">
                        <div data-i18n="Reports">Reports</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Warehouse -->
        <li class="menu-item  <?php if (strpos($PAGE_ID, 'INVENTORY') === 0 ||
            strpos($PAGE_ID, 'ORDER_MANAGEMENT') === 0 ||
            strpos($PAGE_ID, 'TRANSFER') === 0 ||
            strpos($PAGE_ID, 'CREATE_TRANSFER') === 0 ||
            strpos($PAGE_ID, 'RFP') === 0 ||
            strpos($PAGE_ID, 'SUPPLIER_LIST') === 0 ||
            strpos($PAGE_ID, 'SUPPLIER_VIEW') === 0 ||
            str_starts_with($PAGE_ID, 'PO_') ||
            str_starts_with($PAGE_ID, 'WAREHOUSE_') ||
            str_starts_with($PAGE_ID, 'SHIPMENT_')
        ) echo 'active open'; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle"> <i class="menu-icon tf-icons ti ti-box"></i>
                <div data-i18n="Supply Chain Management">Supply Chain Management</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item  <?php if ($PAGE_ID == 'INVENTORY_LIST') echo 'active' ?>">
                    <a href="/inventory/list" class="menu-link" target="">
                        <div data-i18n="Inventory">Inventory</div>
                    </a>
                </li>
                <li class="menu-item  <?php if ($PAGE_ID == 'WAREHOUSE_LIST' || $PAGE_ID == 'WAREHOUSE_VIEW') echo 'active' ?>">
                  <a href="/warehouse/list" class="menu-link" target="">
                    <div data-i18n="Warehouse">Warehouse</div>
                  </a>
                </li>
                <li class="menu-item  <?php if ($PAGE_ID == 'TRANSFER_LIST' || $PAGE_ID == 'TRANSFER_NEW' || $PAGE_ID == 'TRANSFER_EDIT') echo 'active' ?>">
                    <a href="/warehouse/transfer/list" class="menu-link" target="">
                        <div data-i18n="Transfers">Transfers</div>
                    </a>
                </li>
                <li class="menu-item  <?php if (str_starts_with($PAGE_ID, 'ORDER_MANAGEMENT_')) echo 'active open' ?>">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Order Management">Order Management</div>
                    </a>
                    <ul class="menu-sub">
                      <li class="menu-item <?php if ($PAGE_ID === 'ORDER_MANAGEMENT_LIST_SALES_ORDER') echo 'active'; ?>">
                      <a href="/order-management/list?filter=SALES_ORDER" class="menu-link">
                        <div data-i18n="Sales Order">Sales Order</div>
                      </a>
                      </li>
                      <li class="menu-item <?php if ($PAGE_ID === 'ORDER_MANAGEMENT_LIST_WH_TRANSFERS') echo 'active'; ?>">
                        <a href="/order-management/list?filter=TRANSFER_WH" class="menu-link">
                          <div data-i18n="Transfer WH">Transfer WH</div>
                        </a>
                      </li>
                      <li class="menu-item <?php if ($PAGE_ID === 'ORDER_MANAGEMENT_LIST_PICK_AND_PACK' || $PAGE_ID === 'ORDER_MANAGEMENT_PICK_AND_PACK_NEW') echo 'active'; ?>">
                        <a href="/order-management/list?filter=ALL" class="menu-link">
                            <div data-i18n="Pick & Pack">Pick & Pack</div>
                        </a>
                      </li>
                    </ul>
                </li>
                <li class="menu-item" <?php if ($PAGE_ID == 'SUPPLIER_LIST' || $PAGE_ID == 'SUPPLIER_VIEW') echo 'active' ?>>
                    <a href="/suppliers/list" class="menu-link">
                        <div data-i18n="Supplier Management">Supplier Management</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="" class="menu-link">
                        <div data-i18n="Demand Forecasting">Demand Forecasting</div>
                    </a>
                </li>
                <li class="menu-item  <?php if (str_starts_with($PAGE_ID, 'RFP_') || str_starts_with($PAGE_ID, 'PO_')) echo 'active open'; ?>">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Procurement">Procurement</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item <?php if ($PAGE_ID === 'RFP_LIST' || $PAGE_ID === 'RFP_EDIT') echo 'active'; ?>">
                            <a href="/rfp/list" class="menu-link">
                                <div data-i18n="List RFPs">List RFPs</div>
                            </a>
                        </li>
                        <li class="menu-item <?php if ($PAGE_ID === 'PO_LIST' || $PAGE_ID === 'PO_EDIT') echo 'active'; ?>">
                            <a href="/purchaseorder/list" class="menu-link">
                                <div data-i18n="List POs">List POs</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item <?php if ($PAGE_ID === 'ORDER_MANAGEMENT_PICK_N_PACK_ORDERS_LIST') echo 'active'; ?>">
                    <a href="/order-management/picknpack/list" class="menu-link">
                        <div data-i18n="Logistics">Logistics</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- Settings -->
<!--        <li class="menu-item  --><?php //if (strpos($PAGE_ID, 'WAREHOUSE') === 0 || strpos($PAGE_ID, 'PAYMENT_TERMS') === 0 || strpos($PAGE_ID, 'DOCUMENT_SETTINGS_EDIT') === 0) echo 'active open'; ?><!--">-->
    <!-- || str_starts_with($PAGE_ID, 'USER_') || strpos($PAGE_ID, 'COMPANY_ASSETS') === 0 -->
        <li class="menu-item  <?php if (str_starts_with($PAGE_ID, 'PAYMENT_TERMS') || str_starts_with($PAGE_ID, 'DOCUMENT_SETTINGS_') || str_starts_with($PAGE_ID, 'ROLES_') || strpos($PAGE_ID, 'CAPABILITY_USER_LIST') === 0) echo 'active open'; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-settings"></i>
                <div data-i18n="Settings">Settings</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if (strpos($PAGE_ID, 'PAYMENT_TERMS') === 0) echo 'active open'; ?>">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Finance & Accounting">Finance & Accounting</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item <?php if ($PAGE_ID === 'PAYMENT_TERMS') echo 'active'; ?>">
                            <a href="/paymentterms/list" class="menu-link">
                                <div data-i18n="Payment Terms">Payment Terms</div>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="menu-sub">
                <!-- <?php if (str_starts_with($PAGE_ID, 'USER_') || strpos($PAGE_ID, 'ROLES_LIST') === 0 || strpos($PAGE_ID, 'CAPABILITY_USER_LIST') === 0) echo 'active open'; ?> -->
                <li class="menu-item <?php if (strpos($PAGE_ID, 'ROLES_LIST') === 0 || strpos($PAGE_ID, 'CAPABILITY_USER_LIST') === 0) echo 'active open'; ?>">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="User Management">User Management</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item <?php if (str_starts_with($PAGE_ID, 'CAPABILITY_')) echo 'active'; ?>">
                            <a href="/roles/users/list" class="menu-link">
                                <div data-i18n="User Permissions">User Permissions</div>
                            </a>
                        </li>
                    </ul>
                    <ul class="menu-sub">
                        <?php if (is_ceo() || has_permission('rolesAndPermissions', 'view')) { ?>
                        <li class="menu-item <?php if ($PAGE_ID === 'ROLES_LIST') echo 'active'; ?>">
                            <a href="/roles/list" class="menu-link">
                                <div data-i18n="Roles & Permissions">Roles & Permissions</div>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                <li class="menu-item <?php if ($PAGE_ID === 'DOCUMENT_SETTINGS_EDIT') echo 'active'; ?>">
                    <a href="/document/edit/1" class="menu-link">
                        <div data-i18n="Document Settings">Document Settings</div>
                    </a>
                </li>
            </ul>
        </li>
      <!-- <li class="menu-item <?php if (str_starts_with($PAGE_ID, 'ACCOUNTS_')) echo 'active open'; ?>">
        <a href="javascript:void(0);" class="menu-link menu-toggle"> <i class="menu-icon tf-icons ti ti-receipt-tax"></i>
          <div data-i18n="Accounting">Accounting</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_LIST') echo 'active' ?>">
            <a href="/accounts/list" class="menu-link">
              <div data-i18n="All Accounts">All Accounts</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_PAYABLE_LIST') echo 'active' ?>">
            <a href="/payable/list" class="menu-link">
              <div data-i18n="Accounts Payable">Accounts Payable</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_RECEIVABLE_LIST') echo 'active' ?>">
            <a href="/receivable/list" class="menu-link">
              <div data-i18n="Accounts Receivable">Accounts Receivable</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_GENERAL_LEDGER_LIST') echo 'active' ?>">
            <a href="/ledger/list" class="menu-link">
              <div data-i18n="General Ledger">General Ledger</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_CUMULATIVE_CASHFLOW_LIST') echo 'active' ?>">
            <a href="/cashflow/list" class="menu-link">
              <div data-i18n="Cash Flow Statement">Cash Flow Statement</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_NET_VAT_RETURNS_LIST' || $PAGE_ID == 'ACCOUNTS_VAT_SUMMARY') echo 'active' ?>">
            <a href="/vatreturns/list" class="menu-link">
              <div data-i18n="VAT Returns">VAT Returns</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_INVOICE_ARCHIVAL_LIST' || $PAGE_ID == 'ACCOUNTS_INVOICE_ARCHIVAL_SUMMARY') echo 'active' ?>">
            <a href="/invoicearchival/list" class="menu-link">
              <div data-i18n="Invoice Archive">Invoice Archive</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_BALANCE_SHEET') echo 'active' ?>">
            <a href="/balancesheet/list" class="menu-link">
              <div data-i18n="Balance Sheet">Balance Sheet</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_EXECUTIVE_SUMMARY_STATEMENT') echo 'active' ?>">
            <a href="/executivesummary/statement" class="menu-link">
              <div data-i18n="Executive Summary">Executive Summary</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_PARTNER_LEDGER_REPORT') echo 'active' ?>">
            <a href="/partnerledger/report" class="menu-link">
              <div data-i18n="Partner Ledger Report">Partner Ledger Report</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_PROFIT_LOSS_STATEMENT') echo 'active' ?>">
            <a href="/profitloss/statement" class="menu-link">
              <div data-i18n="P&L Statement">P&L Statement</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_TRIAL_BALANCE') echo 'active' ?>">
            <a href="/trialbalance/list" class="menu-link">
              <div data-i18n="Trial Balance">Trial Balance</div>
            </a>
          </li>
        </ul>
      </li> -->
    </ul>
    <?php
}

//Consultation
if ($_SESSION[SES_SELECTED_COMPANY] == 2) {
    ?>
    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item <?php if ($PAGE_ID == 'DASHBOARD') echo 'active' ?>">
            <a href="/dashboard" class="menu-link"> <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        <!--Scheduler-->
        <li class="menu-item <?php if ($PAGE_ID == 'CONSULTATION_SCHEDULER') echo 'active' ?>">
            <a href="/consultation/scheduler" class="menu-link"> <i class="menu-icon tf-icons ti ti-calendar-time"></i>
                <div data-i18n="Scheduler">Scheduler</div>
            </a>
        </li>
        <!-- HR Payroll -->
        <!-- <li class="menu-item  <?php if (str_starts_with($PAGE_ID, 'USER_') || strpos($PAGE_ID, 'COMPANY_ASSETS') === 0 || strpos($PAGE_ID, 'HR_SALARY_PAYROLL') === 0 || strpos($PAGE_ID, 'HR_ATTENDANCE') === 0) echo 'active open'; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-clipboard-list"></i>
                <div data-i18n="HR Workforce">HR Workforce</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if (str_starts_with($PAGE_ID, 'USER_')) echo 'active'; ?>">
                    <a href="/users/list" class="menu-link">
                        <div data-i18n="Employees">Employees</div>
                    </a>
                </li>
                <li class="menu-item  <?php if ($PAGE_ID == 'HR_ATTENDANCE') echo 'active' ?>">
                    <a href="/attendance" class="menu-link">
                        <div data-i18n="Attendance">Attendance</div>
                    </a>
                </li>
                <li class="menu-item  <?php if ($PAGE_ID == 'HR_SALARY_PAYROLL') echo 'active' ?>">
                    <a href="/payroll" class="menu-link">
                        <div data-i18n="Payroll">Payroll</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID === 'COMPANY_ASSETS') echo 'active'; ?>">
                    <a href="/companyassets/list" class="menu-link">
                        <div data-i18n="Company Assets">Company Assets</div>
                    </a>
                </li>
            </ul>
        </li> -->
        <!-- Chat -->
        <li class="menu-item <?php if ($PAGE_ID == 'CHAT') echo 'active' ?>">
            <a href="/chat" class="menu-link"> <i class="menu-icon tf-icons ti ti-message-2"></i>
                <div data-i18n="Chat">Chat</div>
            </a>
        </li>
        <!--CUSTOMERS-->
        <li class="menu-item  <?php if (strpos($PAGE_ID, 'CUSTOMER_') === 0) echo 'active open'; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-building"></i>
                <div data-i18n="Customers">Customers</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if ($PAGE_ID == 'CUSTOMER_LIST') echo 'active' ?>">
                    <a href="/customers/list" class="menu-link"><i class="menu-icon tf-icons ti ti-building"></i>
                        <div data-i18n="All Customers">All Customers</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == 'CUSTOMER_NEW') echo 'active' ?>">
                    <a href="/customers/new" class="menu-link"><i class="menu-icon tf-icons ti ti-building"></i>
                        <div data-i18n="Add Customer">Add Customer</div>
                    </a>
                </li>
            </ul>
        </li>
        <!--Services / Products-->
        <li class="menu-item <?php if ($PAGE_ID == 'CONSULTATION_SERVICES' or $PAGE_ID == 'CONSULTATION_CATEGORIES') echo 'active open'; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-package"></i>
                <div data-i18n="Services / Products">Services / Products</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if ($PAGE_ID == 'CONSULTATION_SERVICES') echo 'active' ?>">
                    <a href="/consultation/services" class="menu-link">
                        <div data-i18n="Services / Products">Services / Products</div>
                    </a>
                </li>
                <li class="menu-item  <?php if ($PAGE_ID == 'CONSULTATION_CATEGORIES') echo 'active' ?>">
                    <a href="/consultation/categories" class="menu-link">
                        <div data-i18n="Categories">Categories</div>
                    </a>
                </li>
            </ul>
        </li>
        <!--PROPOSALS-->
        <li class="menu-item <?php if (str_starts_with($PAGE_ID, 'CONSULTATION_PROPOSAL_')) echo 'active' ?>">
            <a href="/consultation/proposal/list" class="menu-link"> <i class="menu-icon tf-icons ti ti-file-isr"></i>
                <div data-i18n="Proposals">Proposals</div>
            </a>
        </li>
        <li class="menu-item <?php if (str_starts_with($PAGE_ID, 'CONSULTATION_SMA_')) echo 'active' ?>">
            <a href="/consultation/sma/list" class="menu-link"> <i class="menu-icon tf-icons ti ti-file-check"></i>
                <div data-i18n="SMA">SMA</div>
            </a>
        </li>
        <!-- Quotations -->
        <li class="menu-item  <?php if ($PAGE_ID == 'CONSULTATION_QUOTATION_LIST') echo 'active open' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-file-text"></i>
                <div data-i18n="Quotation">Quotation</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item  <?php if ($PAGE_ID == 'CONSULTATION_QUOTATION_LIST' and !isset($_GET['filter'])) echo 'active' ?>">
                    <a href="/consultation/quotation/list" class="menu-link">
                        <div data-i18n="All Quotations">All Quotations</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == 'CONSULTATION_QUOTATION_LIST' and (isset($_GET['filter']) && $_GET['filter'] === 'DRAFT')) echo 'active' ?> ">
                    <a href="/consultation/quotation/list?filter=DRAFT" class="menu-link">
                        <div data-i18n="Drafts">Drafts</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == 'CONSULTATION_QUOTATION_LIST' and (isset($_GET['filter']) && $_GET['filter'] === 'ACTIVE')) echo 'active' ?> ">
                    <a href="/consultation/quotation/list?filter=ACTIVE" class="menu-link">
                        <div data-i18n="Active">Active</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == 'CONSULTATION_QUOTATION_LIST' and (isset($_GET['filter']) && $_GET['filter'] === 'CONFIRMED')) echo 'active' ?> ">
                    <a href="/consultation/quotation/list?filter=CONFIRMED" class="menu-link">
                        <div data-i18n="Confirmed">Confirmed</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/consultation/quotation/list" class="menu-link">
                        <div data-i18n="Quotation Reports">Quotation Reports</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- Sales -->
        <li class="menu-item <?php if (strpos($PAGE_ID, 'CONSULTATION_SALESORDER') === 0) echo 'active open' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
                <div data-i18n="Sales">Sales</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if ($PAGE_ID == 'CONSULTATION_SALESORDER_LIST' || $PAGE_ID == 'CONSULTATION_SALESORDER_EDIT') echo 'active' ?>">
                    <a href="/consultation/salesorders/list" class="menu-link">
                        <div data-i18n="Active Sales Order">Active Sales Order</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/under-development" class="menu-link" target="_blank">
                        <div data-i18n="Sales Reports">Sales Reports</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Invoice -->
        <li class="menu-item <?php if (str_starts_with($PAGE_ID, 'INVOICE_') || str_starts_with($PAGE_ID, 'CONSULTATION_INVOICE_')) echo 'active open' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-receipt"></i>
                <div data-i18n="Invoice">Invoice</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if ($PAGE_ID == 'INVOICE_LIST' || $PAGE_ID == 'CONSULTATION_INVOICE_EDIT') echo 'active' ?>">
                    <a href="/invoice/list" class="menu-link">
                        <div data-i18n="All Invoices">All Invoices</div>
                    </a>
                </li>
            </ul>
        </li>
        <!--Purchase Orders-->
        <!--    <li class="menu-item">-->
        <!--      <a href="javascript:void(0);" class="menu-link menu-toggle">-->
        <!--        <i class="menu-icon tf-icons ti ti-file-invoice"></i>-->
        <!--        <div data-i18n="Purchase Order">Purchase Order</div>-->
        <!--      </a>-->
        <!--      <ul class="menu-sub">-->
        <!--        <li class="menu-item">-->
        <!--          <a href="../front-pages/landing-page.html" class="menu-link" target="_blank">-->
        <!--            <div data-i18n="New PO">New PO</div>-->
        <!--          </a>-->
        <!--        </li>-->
        <!--        <li class="menu-item">-->
        <!--          <a href="../front-pages/pricing-page.html" class="menu-link" target="_blank">-->
        <!--            <div data-i18n="Active PO">Active PO</div>-->
        <!--          </a>-->
        <!--        </li>-->
        <!--        <li class="menu-item">-->
        <!--          <a href="../front-pages/pricing-page.html" class="menu-link" target="_blank">-->
        <!--            <div data-i18n="Reports">Reports</div>-->
        <!--          </a>-->
        <!--        </li>-->
        <!--      </ul>-->
        <!--    </li>-->

        <!--Procurement-->
        <li class="menu-item  <?php if ((str_starts_with($PAGE_ID, "CONSULTATION_RFP_")) || str_starts_with($PAGE_ID, "CONSULTATION_PO_")) echo 'active open'; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-checklist"></i>
                <div data-i18n="Procurement">Procurement</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item" <?php if ($PAGE_ID == 'SUPPLIER_LIST' || $PAGE_ID == 'SUPPLIER_VIEW') echo 'active' ?>>
                    <a href="/suppliers/list" class="menu-link">
                        <div data-i18n="Supplier Management">Supplier Management</div>
                    </a>
                </li>
                <li class="menu-item <?php if (str_starts_with($PAGE_ID, 'CONSULTATION_RFP_')) echo 'active' ?>">
                    <a href="/consultation/rfp/list" class="menu-link"><i class="menu-icon tf-icons ti ti-file-text"></i>
                        <div data-i18n="List RFPs">List RFPs</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == 'CONSULTATION_PO_LIST') echo 'active' ?>">
                    <a href="/consultation/purchaseorder/list" class="menu-link"><i class="menu-icon tf-icons ti ti-file-invoice"></i>
                        <div data-i18n="List POs">List POs</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- Teams -->
        <li class="menu-item  <?php if (strpos($PAGE_ID, 'CONSULTATION_TEAM_') === 0) echo 'active open'; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-users-group"></i>
                <div data-i18n="Team">Team</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if ($PAGE_ID == 'CONSULTATION_TEAM_') echo 'active' ?>">
                    <a href="/consultation/team/list" class="menu-link" target="">
                        <div data-i18n="Team Members">Team Members</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link" target="">
                        <div data-i18n="Time Sheet">Time Sheet</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- Projects -->
        <li class="menu-item  <?php if (str_starts_with($PAGE_ID, "PROJECT")) echo 'active open'; ?>">
            <a href="/projects" class="menu-link "> <i class="menu-icon tf-icons ti ti-briefcase-2"></i>
                <div data-i18n="Projects">Projects</div>
            </a>
        </li>
        <!-- Settings -->
        <li class="menu-item  <?php if ((str_starts_with($PAGE_ID, "SETTINGS")) || strpos($PAGE_ID, 'WAREHOUSE') === 0 || strpos($PAGE_ID, 'PAYMENT_TERMS') === 0 || strpos($PAGE_ID, 'DOCUMENT_SETTINGS_EDIT') === 0 || strpos($PAGE_ID, 'COMPANY_ASSETS') === 0 || str_starts_with($PAGE_ID, 'USER_')) echo 'active open'; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-settings"></i>
                <div data-i18n="Settings">Settings</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if (str_starts_with($PAGE_ID, 'USER_') || strpos($PAGE_ID, 'COMPANY_ASSETS') === 0) echo 'active open'; ?>">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="User Management">User Management</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item <?php if (str_starts_with($PAGE_ID, 'USER_')) echo 'active'; ?>">
                            <a href="/users/list" class="menu-link">
                                <div data-i18n="Users">Users</div>
                            </a>
                        </li>
                    </ul>
                    <ul class="menu-sub">
                        <li class="menu-item <?php if ($PAGE_ID === 'COMPANY_ASSETS') echo 'active'; ?>">
                            <a href="/companyassets/list" class="menu-link">
                                <div data-i18n="Company Assets">Company Assets</div>
                            </a>
                        </li>
                    </ul>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="#" class="menu-link">
                                <div data-i18n="Roles & Permissions">Roles & Permissions</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item <?php if (strpos($PAGE_ID, 'PAYMENT_TERMS') === 0) echo 'active open'; ?>">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Finance & Accounting">Finance & Accounting</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item <?php if ($PAGE_ID === 'PAYMENT_TERMS') echo 'active'; ?>">
                            <a href="/paymentterms/list" class="menu-link">
                                <div data-i18n="Payment Terms">Payment Terms</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item <?php if ($PAGE_ID === 'DOCUMENT_SETTINGS_EDIT') echo 'active'; ?>">
                    <a href="/document/edit/1" class="menu-link">
                        <div data-i18n="Document Settings">Document Settings</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link" target="">
                        <div data-i18n="Contract Templates">Contract Templates</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == "SETTINGS_CONSULTATION_PROPOSAL_NEW_EDIT") echo 'active' ?>">
                    <a href="/consultation/proposaltemplates" class="menu-link" target="">
                        <div data-i18n="Proposal Templates">Proposal Templates</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == "SETTINGS_CONSULTATION_SMA_NEW_EDIT") echo 'active' ?>">
                    <a href="/consultation/smatemplates" class="menu-link" target="">
                        <div data-i18n="SMA Templates">SMA Templates</div>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
    <?php
}

//E-commerce
if ($_SESSION[SES_SELECTED_COMPANY] == 6) {
    ?>
    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item <?php if ($PAGE_ID == 'DASHBOARD') echo 'active' ?>">
            <a href="/dashboard" class="menu-link"> <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        <!--CUSTOMERS-->
        <li class="menu-item  <?php if (strpos($PAGE_ID, 'CUSTOMER_') === 0) echo 'active open'; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-building"></i>
                <div data-i18n="Customers">Customers</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if ($PAGE_ID == 'CUSTOMER_LIST') echo 'active' ?>">
                    <a href="/customers/list" class="menu-link"><i class="menu-icon tf-icons ti ti-building"></i>
                        <div data-i18n="All Customers">All Customers</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID == 'CUSTOMER_NEW') echo 'active' ?>">
                    <a href="/customers/new" class="menu-link"><i class="menu-icon tf-icons ti ti-building"></i>
                        <div data-i18n="Add Customer">Add Customer</div>
                    </a>
                </li>
            </ul>
        </li>
        <!--SpareParts-->
        <li class="menu-item <?php if (strpos($PAGE_ID, 'SPARE') === 0) echo 'active open'; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle"> <i class="menu-icon tf-icons ti ti-tool"></i>
                <div data-i18n="Spare Parts">Spare Parts</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if ($PAGE_ID == SPARE_PART_LIST || $PAGE_ID == SPARE_PART_VIEW) echo 'active' ?>">
                    <a href="/spareparts/list" class="menu-link" target="">
                        <div data-i18n="Spare Parts">Products</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/brands/list" class="menu-link">
                        <div data-i18n="Brands">Brands</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- Orders -->
        <!-- <li class="menu-item  <?php if (str_starts_with($PAGE_ID, 'ORDER_MANAGEMENT_')) echo 'active open' ?>">
                    <a href="javascript:void(0);" class="menu-link menu-toggle"><i class="menu-icon tf-icons ti ti-tool"></i>
                        <div data-i18n="Order Management">Order Management</div>
                    </a>
                    <ul class="menu-sub">
                      <li class="menu-item <?php if ($PAGE_ID === 'ORDER_MANAGEMENT_LIST_SALES_ORDER') echo 'active'; ?>">
                      <a href="/order-management/list?filter=SALES_ORDER" class="menu-link">
                        <div data-i18n="Sales Order">E-commerce</div>
                      </a>
                      </li>
                      <li class="menu-item <?php if ($PAGE_ID === 'ORDER_MANAGEMENT_LIST_WH_TRANSFERS') echo 'active'; ?>">
                        <a href="/order-management/list?filter=TRANSFER_WH" class="menu-link">
                          <div data-i18n="Transfer WH">Transfer WH</div>
                        </a>
                      </li>
                      <li class="menu-item <?php if ($PAGE_ID === 'ORDER_MANAGEMENT_LIST_PICK_AND_PACK' || $PAGE_ID === 'ORDER_MANAGEMENT_PICK_AND_PACK_NEW') echo 'active'; ?>">
                        <a href="/order-management/list?filter=ALL" class="menu-link">
                            <div data-i18n="Pick & Pack">Pick & Pack</div>
                        </a>
                      </li>
                    </ul>
        </li> -->
    </ul>
    <?php
}
//Holding
if (getCompanyNameByID($_SESSION[SES_SELECTED_COMPANY]) == 'GrandMaster Holding Co') {
    ?>
    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item <?php if ($PAGE_ID == 'DASHBOARD') echo 'active' ?>">
            <a href="/dashboard" class="menu-link"> <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        <!-- HR Payroll -->
        <li class="menu-item  <?php if (str_starts_with($PAGE_ID, 'USER_') || strpos($PAGE_ID, 'COMPANY_ASSETS') === 0 || strpos($PAGE_ID, 'HR_SALARY_PAYROLL') === 0 || strpos($PAGE_ID, 'HR_ATTENDANCE') === 0) echo 'active open'; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-clipboard-list"></i>
                <div data-i18n="HR Workforce">HR Workforce</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php if (str_starts_with($PAGE_ID, 'USER_')) echo 'active'; ?>">
                    <a href="/users/list" class="menu-link">
                        <div data-i18n="Employees">Employees</div>
                    </a>
                </li>
                <li class="menu-item  <?php if ($PAGE_ID == 'HR_ATTENDANCE') echo 'active' ?>">
                    <a href="/attendance" class="menu-link">
                        <div data-i18n="Attendance">Attendance</div>
                    </a>
                </li>
                <li class="menu-item  <?php if ($PAGE_ID == 'HR_SALARY_PAYROLL') echo 'active' ?>">
                    <a href="/payroll" class="menu-link">
                        <div data-i18n="Payroll">Payroll</div>
                    </a>
                </li>
                <li class="menu-item <?php if ($PAGE_ID === 'COMPANY_ASSETS') echo 'active'; ?>">
                    <a href="/companyassets/list" class="menu-link">
                        <div data-i18n="Company Assets">Company Assets</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- Accounting -->
        <li class="menu-item <?php if (str_starts_with($PAGE_ID, 'ACCOUNTS_')) echo 'active open'; ?>">
        <a href="javascript:void(0);" class="menu-link menu-toggle"> <i class="menu-icon tf-icons ti ti-receipt-tax"></i>
          <div data-i18n="Accounting">Accounting</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_LIST') echo 'active' ?>">
            <a href="/accounts/list" class="menu-link">
              <div data-i18n="All Accounts">All Accounts</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_PAYABLE_LIST') echo 'active' ?>">
            <a href="/payable/list" class="menu-link">
              <div data-i18n="Accounts Payable">Accounts Payable</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_RECEIVABLE_LIST') echo 'active' ?>">
            <a href="/receivable/list" class="menu-link">
              <div data-i18n="Accounts Receivable">Accounts Receivable</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_GENERAL_LEDGER_LIST') echo 'active' ?>">
            <a href="/ledger/list" class="menu-link">
              <div data-i18n="General Ledger">General Ledger</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_CUMULATIVE_CASHFLOW_LIST') echo 'active' ?>">
            <a href="/cashflow/list" class="menu-link">
              <div data-i18n="Cash Flow Statement">Cash Flow Statement</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_NET_VAT_RETURNS_LIST' || $PAGE_ID == 'ACCOUNTS_VAT_SUMMARY') echo 'active' ?>">
            <a href="/vatreturns/list" class="menu-link">
              <div data-i18n="VAT Returns">VAT Returns</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_INVOICE_ARCHIVAL_LIST' || $PAGE_ID == 'ACCOUNTS_INVOICE_ARCHIVAL_SUMMARY') echo 'active' ?>">
            <a href="/invoicearchival/list" class="menu-link">
              <div data-i18n="Invoice Archive">Invoice Archive</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_BALANCE_SHEET') echo 'active' ?>">
            <a href="/balancesheet/list" class="menu-link">
              <div data-i18n="Balance Sheet">Balance Sheet</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_EXECUTIVE_SUMMARY_STATEMENT') echo 'active' ?>">
            <a href="/executivesummary/statement" class="menu-link">
              <div data-i18n="Executive Summary">Executive Summary</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_PARTNER_LEDGER_REPORT') echo 'active' ?>">
            <a href="/partnerledger/report" class="menu-link">
              <div data-i18n="Partner Ledger Report">Partner Ledger Report</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_PROFIT_LOSS_STATEMENT') echo 'active' ?>">
            <a href="/profitloss/statement" class="menu-link">
              <div data-i18n="P&L Statement">P&L Statement</div>
            </a>
          </li>
          <li class="menu-item <?php if ($PAGE_ID == 'ACCOUNTS_TRIAL_BALANCE') echo 'active' ?>">
            <a href="/trialbalance/list" class="menu-link">
              <div data-i18n="Trial Balance">Trial Balance</div>
            </a>
          </li>
        </ul>
      </li>
    </ul>
    <?php
}
?>