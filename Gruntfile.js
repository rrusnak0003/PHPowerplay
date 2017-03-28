module.exports = function(grunt){

    //load the tasks 
    grunt.loadNpmTasks('grunt-sass');
    
    //project configuration
    grunt.initConfig({
        sass:   {

			options: {
				style: 'expanded',
				sourceMap: true
				},
			dist: {
			    files: {
				    'assets/css/powerplay.css': 'assets/scss/powerplay.scss',
			    }
			}
		}
	});            
    
    grunt.registerTask('default', ['sass']);
    grunt.util.linefeed = '\n';
};