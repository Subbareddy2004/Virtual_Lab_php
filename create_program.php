<?php
$title = "Create New Program";
include 'includes/header.php';
include 'includes/db.php';

$languages = ['c', 'cpp', 'java', 'python'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['program_name'];
    $description = $_POST['description'];
    $language = $_POST['language'];

    $stmt = $pdo->prepare("INSERT INTO programs (name, description, language) VALUES (?, ?, ?)");
    $stmt->execute([$name, $description, $language]);

    header("Location: index.php");
    exit;
}
?>

<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6">Create New Program</h1>
    <form action="" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="program_name">
                Enter a short Program Name:
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="program_name" name="program_name" type="text" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                Description of Program:
            </label>
            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="description" name="description" rows="3" required></textarea>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="language">
                Select Language:
            </label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="language" name="language" required>
                <?php foreach ($languages as $lang): ?>
                    <option value="<?php echo $lang; ?>"><?php echo strtoupper($lang); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Create Program
            </button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
