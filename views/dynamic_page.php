<?php
$slug = $_GET['slug'] ?? null;
if (!$slug) {
    echo "Pagina nu a fost găsită.";
    return;
}

$stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ?");
$stmt->execute([$slug]);
$page = $stmt->fetch();

if (!$page) {
    echo "Pagina nu a fost găsită.";
    return;
}
?>

<div class="max-w-4xl mx-auto px-6 py-20">
    <h1 class="text-4xl font-extrabold mb-10 tracking-tight text-center <?php echo ($theme === 'premium' ? 'premium-glow' : ''); ?>">
        <?php echo htmlspecialchars($page['title']); ?>
    </h1>

    <div class="admin-card p-10 shadow-2xl prose prose-lg <?php echo ($theme === 'premium' || $theme === 'dark' ? 'prose-invert' : ''); ?> max-w-none">
        <?php echo $page['content']; ?>
    </div>
</div>
