const { exec } = require("child_process");

exec("vite build", (err, stdout, stderr) => {
  let output = stdout + stderr;
  // Remove blocos de aviso Sass/Bootstrap
  output = output.replace(/[\s\S]*?@import "mixins\/banner";[\s\S]*?root stylesheet\s*/g, "");
  output = output.replace(/Deprecation Warning[\s\S]*?root stylesheet\s*/g, "");
  output = output.replace(/More info[\s\S]*?root stylesheet\s*/g, "");
  output = output.replace(/Use color\.mix instead\.[\s\S]*?root stylesheet\s*/g, "");
  output = output.replace(/Use math\.unit instead\.[\s\S]*?root stylesheet\s*/g, "");
  output = output.replace(/color\.channel\([\s\S]*?root stylesheet\s*/g, "");
  output = output.replace(/Run in verbose mode to see all warnings\./g, "");
  console.log(output);
});