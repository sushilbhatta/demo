<?php
$achievment = [
    "satisfied_client" => [
        'value' => '1,000+',
        "title" => 'Satisfied Clients'
    ],
    "total_cleaning_hr" => [
        'value' => '10,000+',
        "title" => 'Hour Of Cleaning Per Year'
    ],
    "eco_friendly" => [
        'value' => '100%',
        "title" => 'Eco-Friendly Products'
    ],
    "avability" => [
        'value' => '100%',
        "title" => 'Availability'
    ],
];
?>
<!-- achievment section -->
<section class="achievement">
    <?php foreach ($achievment as $items): ?>
        <div>
            <h2 class="achievement-value"><?php echo $items['value']; ?></h2>
            <p class="achievement-title"><?php echo $items['title']; ?></p>
        </div>
    <?php endforeach; ?>
</section>