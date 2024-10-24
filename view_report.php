<?php
$title = "Program Report";
include 'includes/header.php';
include 'includes/db.php';

$id = $_GET['id'] ?? '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM programs WHERE id = ?");
    $stmt->execute([$id]);
    $program = $stmt->fetch();
}
?>

<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6">Report for <?php echo htmlspecialchars($program['name'] ?? ''); ?></h1>
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <p class="mb-2"><strong>Date Created:</strong> <?php echo $program['created_at']; ?></p>
        <p class="mb-2"><strong>Program Description:</strong> <?php echo htmlspecialchars($program['description']); ?></p>
        <p class="mb-2"><strong>Language:</strong> <?php echo strtoupper($program['language']); ?></p>
    </div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-2">Aim and Algorithm:</h2>
        <pre class="bg-gray-100 p-4 rounded"><?php echo htmlspecialchars($program['aim']); ?></pre>
    </div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-2">Program:</h2>
        <pre class="bg-gray-100 p-4 rounded"><?php echo htmlspecialchars($program['code']); ?></pre>
    </div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-2">Output:</h2>
        <pre class="bg-gray-100 p-4 rounded"><?php echo htmlspecialchars($program['output']); ?></pre>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
