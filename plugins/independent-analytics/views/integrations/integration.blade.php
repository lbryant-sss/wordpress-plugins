@php /** @var \IAWP\Integrations\Integration $integration */ @endphp

<?php 
$class = '';
if ($integration->activated()) {
    $class = 'active';
    if (iawp_is_pro()) {
        $class .= ' tracking';
    } else {
        $class .= ' not-tracking';
    }
}
?>
<div class="iawp-integration {{ esc_attr($class) }}">
    <p class="iawp-plugin-icon">{!! $integration->icon() !!}</p>
    <p class="iawp-plugin-name">{{ esc_html($integration->name()) }}</p>
</div>
