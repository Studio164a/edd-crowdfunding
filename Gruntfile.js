/*global module:false*/
/*global require:false*/
/*jshint -W097*/
"use strict";

module.exports = function(grunt) {
 
    // load all grunt tasks
    require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);
 
    grunt.initConfig({
 
        // watch for changes and trigger compass, jshint, uglify and livereload
        watch: {                        
            compass: {
                files: ['compass/**/*.{scss,sass}'],
                tasks: ['compass']
            },
            // js: {
            //     files: '<%= jshint.all %>',
            //     tasks: ['jshint', 'uglify']
            // },
            // css: {
            //     files: ['assets/css/*.css'],
            //     tasks: ['cssmin']
            // },
            // sync_admin_css: {
            //     files: [
            //         'compass/css/admin/**'
            //     ], 
            //     tasks: ['sync:sync_admin_css']
            // }, 
            sync_public_css: {
                files: [
                    'compass/css/public/**'
                ], 
                tasks: ['sync:sync_public_css']
            },
            sync: {
                files: [
                    'admin/', 
                    'admin/**', 
                    '!admin/assets/compass', 
                    '!admin/assets/compass/**', 
                    '!admin/assets/scss', 
                    '!admin/assets/scss/**',
                    'includes', 
                    'includes/**', 
                    'languages', 
                    'languages/**', 
                    'public', 
                    'public/**', 
                    '!public/assets/compass', 
                    '!public/assets/compass/**', 
                    '!public/assets/scss', 
                    '!public/assets/scss/**', 
                    'edd-crowdfunding.php'
                ],
                tasks: ['sync:dist']
            }     
        },

        // Sync
        sync: {    
            sync_admin_css: {
                files: [
                    {
                        cwd: 'compass/css/admin',
                        src: [ '**' ], 
                        dest: 'admin/assets/css'
                    }
                ]
            },   
            sync_public_css: {
                files: [
                    {
                        cwd: 'compass/css/public',
                        src: [ '**' ], 
                        dest: 'public/assets/css'
                    }
                ]
            },
            dist: {
                files: [
                    // includes files within path
                    {
                        src: [  
                            'admin/', 
                            'admin/**', 
                            '!admin/assets/compass', 
                            '!admin/assets/compass/**', 
                            '!admin/assets/scss', 
                            '!admin/assets/scss/**',
                            'includes', 
                            'includes/**', 
                            'languages', 
                            'languages/**', 
                            'public', 
                            'public/**', 
                            '!public/assets/compass', 
                            '!public/assets/compass/**', 
                            '!public/assets/scss', 
                            '!public/assets/scss/**', 
                            'charitable.php'                                
                        ], 
                        dest: '../../plugins/charitable'
                    }
                ], 
                verbose: true, 
                updateAndDelete: true
            }
        },
 
        // compass and scss
        compass: {
            dist: {
                options: {
                    config: 'config.rb',
                    force: true
                }
            }
        },
 
        // javascript linting with jshint
        jshint: {
            options: {
                jshintrc: '.jshintrc',
                "force": true
            },
            all: [
                'Gruntfile.js'
            ]
        },        

        // uglify to concat, minify, and make source maps
        uglify: {
            dist: {
                files: {
                    'admin/assets/js/edd-crowdfunding-admin.min.js': 'admin/assets/js/edd-crowdfunding-admin.js'
                }
            }
        },

        // minify CSS
        cssmin: {
            minify: {
                files: {
                    'public/assets/css/edd-crowdfunding.min.css' : [ 
                        'public/assets/css/edd-crowdfunding.css'
                    ]
                }
            }
        },        

        // make POT file
        makepot: {
            target: {
                options: {
                    cwd: '',                        // Directory of files to internationalize.
                    domainPath: '/languages',       // Where to save the POT file.                    
                    mainFile: 'eddcf.php',     // Main project file.
                    potFilename: 'eddcf.pot',  // Name of the POT file.
                    type: 'wp-plugin',              // Type of project (wp-plugin or wp-theme).
                    updateTimestamp: true           // Whether the POT-Creation-Date should be updated without other changes.
                }
            }
        }

    });

    // register task
    // grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('build', ['jshint', 'uglify', 'makepot']);
};