<template>
    <ul
      class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion"
      id="accordionSidebar"
    >
      <!-- Sidebar - Brand -->
      <router-link
        class="sidebar-brand d-flex align-items-center justify-content-center"
        to="/"
      >
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">PDC</div>
      </router-link>

      <!-- Divider -->
      <hr class="sidebar-divider my-0" />

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <router-link class="nav-link" to="/admin">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></router-link
        >
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider" />

      <!-- Heading -->
      <!-- <div class="sidebar-heading">Interface</div> -->

      <!-- Nav Item - Pages Collapse Menu -->
      <!-- <li class="nav-item">
        <a
          class="nav-link collapsed"
          href="#"
          data-toggle="collapse"
          data-target="#collapseTwo"
          aria-expanded="true"
          aria-controls="collapseTwo"
        >
          <i class="fas fa-fw fa-cog"></i>
          <span>Components</span>
        </a>
        <div
          id="collapseTwo"
          class="collapse"
          aria-labelledby="headingTwo"
          data-parent="#accordionSidebar"
        >
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Custom Components:</h6>
            <router-link class="collapse-item" to="/admin/components/buttons">Buttons</router-link>
            <router-link class="collapse-item" to="/admin/components/cards">Cards</router-link>
          </div>
        </div>
      </li>

      <!-- Nav Item - Utilities Collapse Menu -->
      <!-- <li class="nav-item">
        <a
          class="nav-link collapsed"
          href="#"
          data-toggle="collapse"
          data-target="#collapseUtilities"
          aria-expanded="true"
          aria-controls="collapseUtilities"
        >
          <i class="fas fa-fw fa-wrench"></i>
          <span>Utilities</span>
        </a>
        <div
          id="collapseUtilities"
          class="collapse"
          aria-labelledby="headingUtilities"
          data-parent="#accordionSidebar"
        >
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Custom Utilities:</h6>
            <router-link class="collapse-item" to="/admin/utilities/colors">Colors</router-link>
            <router-link class="collapse-item" to="/admin/utilities/borders">Borders</router-link>
            <router-link class="collapse-item" to="/admin/utilities/animations"
              >Animations</router-link
            >
            <router-link class="collapse-item" to="/admin/utilities/other">Other</router-link>
          </div>
        </div>
      </li> -->

      <!-- Divider -->
      <!-- <hr class="sidebar-divider" /> -->

      <!-- Heading -->
      <!-- <div class="sidebar-heading">Addons</div> -->

      <!-- Nav Item - Pages Collapse Menu -->
      <!-- <li class="nav-item">
        <a
          class="nav-link collapsed"
          href="#"
          data-toggle="collapse"
          data-target="#collapsePages"
          aria-expanded="true"
          aria-controls="collapsePages"
        >
          <i class="fas fa-fw fa-folder"></i>
          <span>Pages</span>
        </a>
        <div
          id="collapsePages"
          class="collapse"
          aria-labelledby="headingPages"
          data-parent="#accordionSidebar"
        >
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Other Pages:</h6>
            <router-link class="collapse-item" to="/admin/pages/page-not-found">404 Page</router-link>
            <router-link class="collapse-item" to="/admin/pages/blank">Blank Page</router-link>
          </div>
        </div>
      </li> -->

      <!-- Nav Item - Tables -->

      <!-- Divider -->
      <!-- <hr class="sidebar-divider" /> -->

      <!-- Heading -->
      <div class="sidebar-heading">Reports</div>

      <!-- Nav Item - Charts Collapse Menu -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          href="#"
          data-toggle="collapse"
          data-target="#collapseCharts"
          aria-expanded="true"
          aria-controls="collapseCharts"
        >
          <i class="fas fa-fw fa-chart-area"></i>
          <span>Charts</span>
        </a>
        <div
          id="collapseCharts"
          class="collapse"
          aria-labelledby="headingCharts"
          data-parent="#accordionSidebar"
        >
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Custom Charts:</h6>
            <!-- QRIS -->
            <a
              class="collapse-item"
              href="#"
              data-toggle="collapse"
              data-target="#collapseQris"
              aria-expanded="true"
              aria-controls="collapseQris"
              >QRIS</a
            >
            <div id="collapseQris" class="collapse" data-parent="#collapseCharts">
              <div class="bg-white py-2 collapse-inner rounded">
                <router-link class="collapse-item" to="/chart/qris-line" target="_blank"
                  >QRIS Line</router-link
                >
                <router-link class="collapse-item" to="/chart/qris-hana" target="_blank"
                  >QRIS Hana</router-link
                >
              </div>
            </div>
            <!-- Biller -->
            <a
              class="collapse-item"
              href="#"
              data-toggle="collapse"
              data-target="#collapseBiller"
              aria-expanded="true"
              aria-controls="collapseBiller"
              >Biller</a
            >
            <div id="collapseBiller" class="collapse" data-parent="#collapseCharts">
              <div class="bg-white py-2 collapse-inner rounded">
                <router-link class="collapse-item" to="/chart/biller-line" target="_blank"
                  >Biller Line</router-link
                >
                <router-link class="collapse-item" to="/chart/biller-hana" target="_blank"
                  >Biller Hana</router-link
                >
              </div>
            </div>
            <!-- Debit -->
            <a
              class="collapse-item"
              href="#"
              data-toggle="collapse"
              data-target="#collapseDebit"
              aria-expanded="true"
              aria-controls="collapseDebit"
              >Debit</a
            >
            <div id="collapseDebit" class="collapse" data-parent="#collapseCharts">
              <div class="bg-white py-2 collapse-inner rounded">
                <router-link class="collapse-item" to="/chart/debit-line" target="_blank"
                  >Debit Line</router-link
                >
                <router-link class="collapse-item" to="/chart/debit-hana" target="_blank"
                  >Debit Hana</router-link
                >
              </div>
            </div>
            <router-link class="collapse-item" to="/chart/slideshow" target="_blank"
              >Slideshow</router-link
            >
            <router-link class="collapse-item" to="/admin/combined-chart"
              >Combined Chart</router-link
            >
          </div>
        </div>
      </li>

      <!-- Nav Item - Tables Collapse Menu -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          href="#"
          data-toggle="collapse"
          data-target="#collapseTables"
          aria-expanded="true"
          aria-controls="collapseTables"
        >
          <i class="fas fa-fw fa-table"></i>
          <span>Tables</span>
        </a>
        <div
          id="collapseTables"
          class="collapse"
          aria-labelledby="headingTables"
          data-parent="#accordionSidebar"
        >
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Data Tables:</h6>
            <router-link class="collapse-item" to="/admin/reports/qris-data">QRIS Data</router-link>
            <router-link class="collapse-item" to="/admin/reports/biller-data">Biller Data</router-link>
            <router-link class="collapse-item" to="/admin/reports/debit-data">Debit Data</router-link>
          </div>
        </div>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider" />

      <!-- Heading -->
      <div class="sidebar-heading">Data Management</div>

      <!-- Nav Item - Data Input -->
      <li class="nav-item">
        <a
          class="nav-link collapsed"
          href="#"
          data-toggle="collapse"
          data-target="#collapseData"
          aria-expanded="true"
          aria-controls="collapseData"
        >
          <i class="fas fa-fw fa-database"></i>
          <span>Data Input</span>
        </a>
        <div
          id="collapseData"
          class="collapse"
          aria-labelledby="headingData"
          data-parent="#accordionSidebar"
        >
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Input Forms:</h6>
            <router-link class="collapse-item" to="/admin/qris-data-form">QRIS Data Form</router-link>
            <router-link class="collapse-item" to="/admin/biller-data-form">Biller Data Form</router-link>
            <router-link class="collapse-item" to="/admin/debit-data-form">Debit Data Form</router-link>
          </div>
        </div>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block" />

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>
    </ul>
    <!-- End of Sidebar -->
</template>

<script>

export default {
    name: 'Sidebar'
}
</script>
