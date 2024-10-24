<?php
$title = "PHP Virtual Lab";
include 'includes/header.php';
include 'includes/db.php';

// Fetch existing programs from the database
$stmt = $pdo->query("SELECT * FROM programs ORDER BY created_at DESC");
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6">Virtual Programming Lab</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-2">Join a Course</h2>
            <p class="mb-4">Click the link to Join with a course currently running.</p>
            <a href="#" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Click Here</a>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-2">Direct Code</h2>
            <p class="mb-4">Click this link to add a new Program without joining Courses</p>
            <a href="create_program.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Click Here</a>
        </div>
    </div>

    <h2 class="text-2xl font-bold mb-4">Existing Programs</h2>
    <div class="overflow-x-auto">
        <table class="w-full bg-white shadow-md rounded-lg">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">SI.No.</th>
                    <th class="py-3 px-6 text-left">Program Name</th>
                    <th class="py-3 px-6 text-left">Description</th>
                    <th class="py-3 px-6 text-left">Language</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php foreach ($programs as $index => $program): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap"><?php echo $index + 1; ?></td>
                    <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($program['name']); ?></td>
                    <td class="py-3 px-6 text-left"><?php echo htmlspecialchars($program['description']); ?></td>
                    <td class="py-3 px-6 text-left"><?php echo strtoupper($program['language']); ?></td>
                    <td class="py-3 px-6 text-center">
                        <a href="run_program.php?id=<?php echo $program['id']; ?>" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 mr-2">Edit</a>
                        <a href="view_report.php?id=<?php echo $program['id']; ?>" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Report</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
