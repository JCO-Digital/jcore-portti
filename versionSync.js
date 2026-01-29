const { readFileSync, writeFileSync } = require("fs");

exports.preCommit = (props) => {
	fileReplace("jcore-portti.php", /Version:.*$/m, `Version: ${props.version}`);
};

const fileReplace = (filename, search, replace) => {
	try {
		const file = readFileSync(filename);
		const updatedFile = file.toString().replace(search, replace);
		writeFileSync(filename, updatedFile);
	} catch (error) {
		console.error(error);
	}
};
