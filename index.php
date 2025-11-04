<?php include 'includes/header.php'; ?>

<main>
    <section class="hero">
        <div class="container">
            <h1>Cumpără și vinde modele 3D de înaltă calitate</h1>
            <p>Răsfoiește catalogul nostru de modele 3D, rig-uri, texturi și scene create de artiști talentați.</p>
            <form action="models.php" method="get" class="search-form">
                <input type="search" name="q" placeholder="Caută modele 3D...">
                <button type="submit" class="btn">Caută</button>
            </form>
        </div>
    </section>

    <section class="featured-models">
        <div class="container">
            <h2>Top Modele</h2>
            <div class="models-grid">
                <?php
                // Date statice pentru demonstrație
                $models = [
                    ['img' => 'https://via.placeholder.com/300x200.png/2b2b2b/e0e0e0?text=Model+1', 'title' => 'Robot de Luptă', 'seller' => 'ArtistX', 'price' => '49.99'],
                    ['img' => 'https://via.placeholder.com/300x200.png/2b2b2b/e0e0e0?text=Model+2', 'title' => 'Mașină Sport', 'seller' => '3DAuto', 'price' => '29.99'],
                    ['img' => 'https://via.placeholder.com/300x200.png/2b2b2b/e0e0e0?text=Model+3', 'title' => 'Personaj Fantasy', 'seller' => 'Creatura', 'price' => '59.00'],
                    ['img' => 'https://via.placeholder.com/300x200.png/2b2b2b/e0e0e0?text=Model+4', 'title' => 'Clădire Modernă', 'seller' => 'Arhitectură3D', 'price' => '79.50'],
                ];

                foreach ($models as $model) :
                ?>
                    <div class="model-card">
                        <img src="<?php echo $model['img']; ?>" alt="<?php echo $model['title']; ?>">
                        <div class="model-info">
                            <h3><?php echo $model['title']; ?></h3>
                            <p class="seller">de <?php echo $model['seller']; ?></p>
                            <p class="price">$<?php echo $model['price']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
