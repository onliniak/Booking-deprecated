<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';
global $post;
global $wpdb;
?>
<!DOCTYPE html>
<html lang="pl">

<head>
  <meta charset="utf-8">

  <link rel="stylesheet" href="css/tui-calendar.min.css">
  <link rel="stylesheet" href="css/micromodal.css">

  <style media="screen">
  @media (min-width: 1200px) {
    .grid-container {
      display: grid;
      grid-template-columns: 800px 400px
    }}

    /* Style the buttons that are used to open and close the accordion panel
              https://www.w3schools.com/howto/howto_js_accordion.asp */
    .accordion {
      background-color: #eee;
      color: #444;
      cursor: pointer;
      padding: 18px;
      width: 100%;
      text-align: left;
      border: none;
      outline: none;
      transition: 0.4s;
      margin: 10px auto
    }

    /* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
    .active,
    .accordion:hover {
      background-color: #ccc;
    }

    /* Style the accordion panel. Note: hidden by default */
    .panel {
      padding: 0 18px;
      background-color: white;
      display: none;
      overflow: hidden;
    }

    .submit_button {
      margin: 10px auto
    }

    .tui-view-7 {
      display: none;
    }

    .tui-full-calendar-time-date-schedule-block {
      width: 106% !important
    }
  </style>
</head>

<body>
  <div class="grid-container">
    <div class="first_column" style="display:none">
      <div class="timeButtons">
        <button onclick="calendar.prev()">
          <svg style="width:20px" viewBox="0 0 24 24">
            <path fill="#000000" d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z" />
          </svg>Poprzedni</button>
        <button onclick="calendar.next()">
          <svg style="width:20px" viewBox="0 0 24 24">
            <path fill="#000000" d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z" />
          </svg>Następny</button>
        <button onclick="calendar.today()">
          <svg style="width:20px" viewBox="0 0 24 24">
            <path fill="#000000" d="M7,10H12V15H7M19,19H5V8H19M19,3H18V1H16V3H8V1H6V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3Z" />
          </svg>Dziś</button>

        <button onclick="calendar.changeView('month', true)">
          <svg style="width:20px" viewBox="0 0 24 24">
            <path fill="#000000" d="M16,5V11H21V5M10,11H15V5H10M16,18H21V12H16M10,18H15V12H10M4,18H9V12H4M4,11H9V5H4V11Z" />
          </svg>Widok miesięczny</button>
        <button onclick="calendar.changeView('week', true)">
          <svg style="width:20px" viewBox="0 0 24 24">
            <path fill="#000000"
              d="M13,5H10A1,1 0 0,0 9,6V18A1,1 0 0,0 10,19H13A1,1 0 0,0 14,18V6A1,1 0 0,0 13,5M20,5H17A1,1 0 0,0 16,6V18A1,1 0 0,0 17,19H20A1,1 0 0,0 21,18V6A1,1 0 0,0 20,5M6,5H3A1,1 0 0,0 2,6V18A1,1 0 0,0 3,19H6A1,1 0 0,0 7,18V6A1,1 0 0,0 6,5Z" />
          </svg>Widok tygodniowy</button>
        <button onclick="calendar.changeView('day', true)">
          <svg style="width:20px" viewBox="0 0 24 24">
    <path fill="#000000" d="M2,3V6H21V3M20,8H3A1,1 0 0,0 2,9V15A1,1 0 0,0 3,16H20A1,1 0 0,0 21,15V9A1,1 0 0,0 20,8M2,21H21V18H2V21Z" />
</svg>Widok dzienny</button>
      </div>
      <div id="calendar"></div>
    </div>

    <div class="second_column">
      <div id="service">
        <fieldset>
          <?php
                        $myposts = get_posts([
                            'posts_per_page' => -1,
                            'post_type' => 'services'
                        ]);
                        foreach ($myposts as $service) {
                            echo '<label id="' . $service->ID . '">' . apply_filters('the_title', $service->post_title);
                            ?>
          <input type="radio" name="service" value="<?= $service->ID ?>" onchange="sessionStorage.setItem('Service_ID', <?= $service->ID ?>)"></label>
          <?php
                               }
                               ?>
        </fieldset>
      </div>
      <div id="animal"></div>
    </div>

    <div class="modal micromodal-slide" id="booking" aria-hidden="true">
      <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="booking-title">
          <h2 class="modal__title" id="booking-title">
            <?php esc_html_e('Szczegóły rezerwacji:', 'grooming') ?>
          </h2>
          <div class="modal__content" id="booking-content">
            <div id="booking-details">
            </div>
            <?php
                            if (is_user_logged_in()) {
                                $current_user = wp_get_current_user();
                                echo esc_html('Witaj, ', 'grooming') . $current_user->display_name;
                            } else {
                                esc_html_e('Zaloguj się lub załóż nowe konto, bez obaw szczegóły rezerwacji zostaną zapisane', 'grooming');
                                ?>
            <button class="accordion"><?php esc_html_e('Zaloguj się:', 'grooming') ?></button>
            <div id="loginme" class="panel">
              <input id="username" type="text" name="username" placeholder="<?php esc_html_e('Użytkownik:', 'grooming') ?>">
              <input id="Pass" type="password" name="password" placeholder="<?php esc_html_e('Hasło:', 'grooming') ?>">
              <br />
              <a class="lost" href="<?php echo wp_lostpassword_url(); ?>"><?php esc_html_e('Zapomniałeś/-aś swojego hasła ?', 'grooming') ?></a>
              <br />
              <span onclick="ajax_login()">Submit</span>
              <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
            </div>

            <button class="accordion"><?php esc_html_e('Zarejestruj się:', 'grooming') ?></button>
            <div class="panel">
              <input id="username_regi" type="text" name="username" placeholder="<?php esc_html_e('Użytkownik:', 'grooming') ?>">
              <input id="email_regi" type="email" name="username" placeholder="<?php esc_html_e('E-mail:', 'grooming') ?>">
              <input id="Pass_regi" type="password" name="password" placeholder="<?php esc_html_e('Hasło:', 'grooming') ?>">
              <br />
              <span onclick="ajax_register();">Submit</span>
            </div>
            <br />
            <?php
                            }
                            ?>
            <span onclick="ajax_login()">Submit</span>
            <input type="hidden" id="booking_save">
          </div>
        </div>
      </div>
    </div>

    <script src="js/tui-code-snippet.min.js" charset="utf-8"></script>
    <script src="js/tui-dom.min.js" charset="utf-8"></script>
    <script src="js/tui-calendar.min.js" charset="utf-8"></script>
    <script src="js/micromodal.min.js" charset="utf-8"></script>
    <script src="js/main.js" charset="utf-8"></script>

    <script type="text/javascript">
      calendar.createSchedules([
            <?php
            $sql = $wpdb-> get_results("
              SELECT time_Start, time_End, Animal_name FROM calendar WHERE Salon_ID = 1 ");

              $c = count($sql);

              for ($i = 0; $i < $c; $i++) {
                $timestamp = strtotime($sql[$i]-> time_Start);
                $timestamp2 = strtotime($sql[$i]-> time_End);

                $arrayName = array(
                  'id' => $i + 1,
                  'calendarId' => 1,
                  'title' => 'Zajęte przez '.esc_html($sql[$i]-> Animal_name),
                  'category' => 'time',
                  'dueDateClass' => '',
                  'start' => date(DATE_ATOM, $timestamp),
                  'end' => date(DATE_ATOM, $timestamp2),
                  'isReadOnly' => 'true'
                );
                if ($i >= 1) {
                  echo ',';
                }
                echo json_encode($arrayName);
              } ?>
            ]);
    </script>
    <small>Icons made by <a href="http://google.github.io/material-design-icons/">Google</a>, licensed under Apache License 2.0</small>
</body>

</html>
