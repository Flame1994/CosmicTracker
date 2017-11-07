<?php
  // =========================================================================
  // ========================== Database Connection ==========================
  // =========================================================================
  // Hostname:Port
  $config["database"]["host"] = "127.0.0.1:3307";
  $config["database"]["user"] = "";
  $config["database"]["passwd"] = "";
  $config["database"]["db"] = "";
  // =========================================================================
  // =========================== Application Data ============================
  // =========================================================================
  // An application will need to be generated to host locally.  Make sure to use the correct callback
  // ($config["app"]["root_dir"]/callback.php) and set the correct permissions.  Auth + Access required.
  // Link: https://developers.eveonline.com/applications/create
  // Root directory for the app
  $config["app"]["root_dir"] = "http://path.to/root";
  $config["app"]["client_id"] = "";
  $config["app"]["secret_key"] = "";
  $config["app"]["permissions"] = array("characterLocationRead",
                                        "characterNavigationWrite",
                                        "esi-ui.write_waypoint.v1");