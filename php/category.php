<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';

$input = file_get_contents('php://input');
$service = filter_var($input, FILTER_VALIDATE_INT);
$category = get_the_terms($service, 'services_group');

echo '<fieldset>';

foreach ($category as $animal) {
    ?>
  <label> <?php esc_html_e($animal->name); ?>
    <input type="radio" name="animal" value="<?php esc_html_e($animal->slug); ?>">
  </label>
  <?php
}
echo '</fieldset>';
