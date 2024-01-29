    <ul class="mega-links">
        <?php
        foreach ($result as $category) {
            $controller = new \App\Http\Controllers\ElasticSearchController();
            $parent_id  = $controller->getParentCategory($category['parent_id']);
        ?>
            <li><a href="#"><?php echo $parent_id; ?></a></li>
        <?php
        }
        ?>
    </ul>