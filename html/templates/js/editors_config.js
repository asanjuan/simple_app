//tinymce config
    

tinymce.init({
    height: 500,
    width: "100%",
    //menubar: false,
    selector: 'textarea.richtext', // Selector de elementos donde se aplicará TinyMCE
    plugins: 'fullscreen code table visualblocks advlist autolink lists link image charmap print preview anchor',
    toolbar: 'undo redo copy cut paste blocks fontfamily fontsize forecolor backcolor | bold italic underline | table bullist numlist outdent indent |'
        +' alignleft aligncenter alignright alignjustify link unlink image code',
    //toolbar_mode: 'wrap',
    table_default_attributes: {
        border: 0 // evita que se agregue el atributo border
    },
    resize: 'both',
    setup: function (editor) {
        editor.on('NodeChange', function (e) {
            // Remueve estilos height en cada cambio de nodo
            editor.dom.select('table, td, th').forEach(function (el) {
            el.style.height = '';
            el.removeAttribute('height');
            });
        });
        }
        

});

    

	//editor de código ACE
    document.addEventListener("DOMContentLoaded", function () {
        /**
         * USANDO CKEDITOR 5
         */
        /*
        document.querySelectorAll('textarea.richtext').forEach((textarea) => {
        ClassicEditor
            .create(textarea)
            .catch(error => console.error(error));
        });
*/
        document.querySelectorAll(".php_editor").forEach(function(div) {
            var editor = ace.edit(div);
            
            editor.setTheme("ace/theme/tomorrow_night_blue");
            editor.session.setMode("ace/mode/php");
            ace.require("ace/ext/language_tools");
            let val = div.getAttribute("data-enabled");
            let readonly = (val === true || val === "true" );
            editor.setOptions({
                readOnly: readonly,
                fontSize: "14px",
                highlightActiveLine: true,
                displayIndentGuides: true, // Líneas guía de indentación
                showPrintMargin: false,
                highlightSelectedWord: true,
                enableBasicAutocompletion: true,  // Sugerencias básicas (palabras en el editor)
                enableLiveAutocompletion: true,   // Autocompletado en vivo
                enableSnippets: true              // Fragmentos de código
            });
            
            var control_name = div.getAttribute("data-control");
            
            var form = document.getElementById("form_edit");
            
            if (form){
                
                var textarea = document.getElementById(control_name);
                form.addEventListener("submit", function () {
                    textarea.value = editor.getValue();
                    
                });
            }

        });
        
        document.querySelectorAll(".js_editor").forEach(function(div) {
            var editor = ace.edit(div);
            editor.setTheme("ace/theme/textmate");
            editor.session.setMode("ace/mode/javascript");
            ace.require("ace/ext/language_tools");
            let val = div.getAttribute("data-enabled");
            let readonly = (val === true || val === "true" );
            editor.setOptions({
                
                readOnly: readonly,
                fontSize: "14px",
                highlightActiveLine: true,
                displayIndentGuides: true, // Líneas guía de indentación
                showPrintMargin: false,
                highlightSelectedWord: true,					
                enableBasicAutocompletion: true,  // Sugerencias básicas (palabras en el editor)
                enableLiveAutocompletion: true,   // Autocompletado en vivo
                enableSnippets: true              // Fragmentos de código
            });
            
            var control_name = div.getAttribute("data-control");
            
            var form = document.getElementById("form_edit");
            
            if (form){
                
                var textarea = document.getElementById(control_name);
                form.addEventListener("submit", function () {
                    textarea.value = editor.getValue();
                    
                });
            }

        });

        document.querySelectorAll(".sql_editor").forEach(function(div) {
            var editor = ace.edit(div);
            editor.setTheme("ace/theme/textmate");
            editor.session.setMode("ace/mode/sql");
            ace.require("ace/ext/language_tools");
            let val = div.getAttribute("data-enabled");
            let readonly = (val === true || val === "true" );
            editor.setOptions({
                readOnly: readonly,
                fontSize: "14px",
                highlightActiveLine: true,
                displayIndentGuides: true, // Líneas guía de indentación
                showPrintMargin: false,
                highlightSelectedWord: true,					
                enableBasicAutocompletion: true,  // Sugerencias básicas (palabras en el editor)
                enableLiveAutocompletion: true,   // Autocompletado en vivo
                enableSnippets: true              // Fragmentos de código
            });
            
            var control_name = div.getAttribute("data-control");
            
            var form = document.getElementById("form_edit");
            
            if (form){
                
                var textarea = document.getElementById(control_name);
                form.addEventListener("submit", function () {
                    textarea.value = editor.getValue();
                    
                });
            }

        });


    }); //DOMContentLoaded
