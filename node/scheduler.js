const { exec } = require('child_process');

function runFile() {
  // Replace 'node' with the path to your Node.js executable if necessary
  const command = 'forever restart geofire.js'; // Replace 'your_file.js' with the path to your Node.js file

  exec(command, (error, stdout, stderr) => {
    if (error) {
      console.error(`Error executing the file: ${error}`);
      return;
    }

    console.log(`File executed successfully: ${stdout}`);
    console.error(`Error output: ${stderr}`);
  });
}

// Run the file immediately
runFile();

// Schedule the file to run every 5 minutes
setInterval(runFile, 5 * 60 * 1000);
