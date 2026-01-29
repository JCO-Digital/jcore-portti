// Import the original config from the @wordpress/scripts package.
const wordpressConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

/**
 * Extend the shared config to include the jcore-media breakpoints.
 * @param {Object} config - The original config.
 * @returns {Object} The extended config.
 */
function extendSharedConfig(config) {
	return {
		...config,
		resolve: {
			alias: {
				media$: path.resolve(
					__dirname,
					'node_modules/@jcodigital/jcore-media/src/media.scss'
				),
			},
		},
	};
}

/**
 * Extend the script config (currently extends with nothing)
 *
 * The Script Config is for non module-files (e.g. non-interactivity API files)
 *
 * @param {Object} config - The original config.
 * @returns {Object} The extended config.
 */
function extendScriptConfig(config) {
	return {
		...config,
	};
}

/**
 * Extend the module config (currently extends with nothing)
 *
 * The Module Config is for module-files (e.g. interactivity API files)
 *
 * @param {Object} config - The original config.
 * @returns {Object} The extended config.
 */
function extendModuleConfig(config) {
	return {
		...config,
		target: ['web'],
	};
}

module.exports = (() => {
	if (Array.isArray(wordpressConfig)) {
		const [scriptConfig, moduleConfig] = wordpressConfig;

		const extendedScriptConfig = extendSharedConfig(
			extendScriptConfig(scriptConfig)
		);
		const extendedModuleConfig = extendSharedConfig(
			extendModuleConfig(moduleConfig)
		);

		return [extendedScriptConfig, extendedModuleConfig];
	} else {
		return extendSharedConfig(extendScriptConfig(wordpressConfig));
	}
})();
