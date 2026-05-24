<?php

if (!defined("IN_FUSION")) { die("Access Denied"); }

function render_page($license = false) {
	
	global $settings;

  echo '<div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="app-shell">
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-panel reveal delay-1">
        <div class="brand">
          <div class="brand-mark">
            <i class="fa-solid fa-cubes-stacked"></i>
          </div>
          <div>
            <h1 class="brand-title">SEO-Fusion</h1>
            <p class="brand-sub">Content Control System</p>
          </div>
        </div>';
        echo showsublinks('', '');
        echo '</div></aside>';

         echo '<main class="main">
      <header class="topbar reveal delay-2">
        <button class="icon-btn mobile-toggle" id="mobileToggle" aria-label="Menü öffnen">
          <i class="fa-solid fa-bars"></i>
        </button>';

        echo '<div class="topbar-actions">
          <button class="icon-btn" aria-label="Kalender">
            <i class="fa-regular fa-calendar"></i>
          </button>
          <button class="icon-btn" aria-label="Benachrichtigungen">
            <i class="fa-regular fa-bell"></i>
          </button>
          <button class="primary-btn">
            <i class="fa-solid fa-plus me-2"></i>Neuer Inhalt
          </button>
          <div class="profile-pill">
            <div class="avatar">LS</div>
            <div class="profile-text">
              <strong>'.$userdata['user_name']."</strong>
              <small></small>
            </div>
          </div>
        </div>
      </header>';

}