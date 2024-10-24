<?php
// Turn off output buffering
ob_start();

$title = "Run Program";
include 'includes/header.php';
include 'includes/db.php';

$id = $_GET['id'] ?? '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM programs WHERE id = ?");
    $stmt->execute([$id]);
    $program = $stmt->fetch();
}

function executeCode($language, $code, $input) {
    $tempDir = sys_get_temp_dir();
    $fileName = uniqid('program_');
    $filePath = $tempDir . DIRECTORY_SEPARATOR . $fileName;
    
    switch ($language) {
        case 'c':
            $sourceFile = $filePath . '.c';
            $exeFile = $filePath;
            file_put_contents($sourceFile, $code);
            exec("gcc $sourceFile -o $exeFile 2>&1", $compileOutput, $compileStatus);
            break;
        case 'cpp':
            $sourceFile = $filePath . '.cpp';
            $exeFile = $filePath;
            file_put_contents($sourceFile, $code);
            exec("g++ $sourceFile -o $exeFile 2>&1", $compileOutput, $compileStatus);
            break;
        case 'java':
            $sourceFile = $tempDir . DIRECTORY_SEPARATOR . 'Main.java';
            file_put_contents($sourceFile, $code);
            exec("javac $sourceFile 2>&1", $compileOutput, $compileStatus);
            $exeFile = "java -cp $tempDir Main";
            break;
        case 'python':
            $sourceFile = $filePath . '.py';
            file_put_contents($sourceFile, $code);
            $compileStatus = 0; // Python doesn't need compilation
            $exeFile = "python $sourceFile";
            break;
        default:
            return ['error' => 'Unsupported language'];
    }
    
    if ($compileStatus !== 0) {
        return ['error' => "Compilation error:\n" . implode("\n", $compileOutput)];
    }
    
    // Execute the compiled program
    $descriptorSpec = [
        0 => ["pipe", "r"],  // stdin
        1 => ["pipe", "w"],  // stdout
        2 => ["pipe", "w"]   // stderr
    ];
    $process = proc_open($exeFile, $descriptorSpec, $pipes);
    
    if (is_resource($process)) {
        fwrite($pipes[0], $input);
        fclose($pipes[0]);
        
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        
        $errorOutput = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        
        $returnValue = proc_close($process);
        
        if ($returnValue !== 0) {
            return ['error' => "Runtime error:\n" . $errorOutput];
        }
        
        return ['output' => $output];
    } else {
        return ['error' => 'Failed to execute the program'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Clear any output that might have been generated
    ob_clean();

    $code = $_POST['code'] ?? '';
    $aim = $_POST['aim'] ?? '';
    $input = $_POST['input'] ?? '';
    $language = $program['language'] ?? '';
    
    if (!$language) {
        echo json_encode(['error' => 'Invalid program or language']);
        exit;
    }

    try {
        // Execute the code
        $result = executeCode($language, $code, $input);
        
        // Save the code, aim, and output
        $stmt = $pdo->prepare("UPDATE programs SET code = ?, aim = ?, output = ? WHERE id = ?");
        $stmt->execute([$code, $aim, $result['output'] ?? '', $id]);
        
        // Send JSON response
        header('Content-Type: application/json');
        echo json_encode($result);
    } catch (Exception $e) {
        // Log the error (in a production environment)
        error_log($e->getMessage());
        
        // Send a generic error message to the client
        echo json_encode(['error' => 'An error occurred while processing your request']);
    }
    exit;
}

// If it's not a POST request, continue with the HTML output
?>

<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6">Program: <?php echo htmlspecialchars($program['name'] ?? ''); ?></h1>
    <form id="codeForm" action="" method="post">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h2 class="text-xl font-semibold mb-2">Type your Code Here</h2>
                <textarea id="code" name="code" rows="10" class="w-full p-2 border rounded"><?php echo htmlspecialchars($program['code'] ?? ''); ?></textarea>
            </div>
            <div>
                <h2 class="text-xl font-semibold mb-2">Type Your Aim and Algorithm Here</h2>
                <textarea id="aim" name="aim" rows="10" class="w-full p-2 border rounded"><?php echo htmlspecialchars($program['aim'] ?? ''); ?></textarea>
            </div>
        </div>
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Give the Inputs (One in each line)</h2>
            <textarea id="input" name="input" rows="5" class="w-full p-2 border rounded"></textarea>
        </div>
        <div class="flex justify-between mb-6">
            <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back</a>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Run Code</button>
            <button type="reset" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Reset</button>
        </div>
    </form>
    <div id="loader" class="loader"></div>
    <div>
        <h2 class="text-xl font-semibold mb-2">Output:</h2>
        <pre id="output" class="bg-gray-100 p-4 rounded"></pre>
    </div>
</div>

<script>
document.getElementById('codeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    var loader = document.getElementById('loader');
    var output = document.getElementById('output');

    // Show the loader and clear the output
    loader.style.display = 'block';
    output.textContent = '';

    fetch('run_program.php?id=<?php echo $id; ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        // Hide the loader
        loader.style.display = 'none';

        if (data.error) {
            output.textContent = 'Error: ' + data.error;
        } else {
            output.textContent = data.output;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        output.textContent = 'An error occurred while processing your request';
        // Hide the loader
        loader.style.display = 'none';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
