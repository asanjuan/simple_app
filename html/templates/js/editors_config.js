//tinymce config
    
/*
tinymce.init({
    height: 500,
    width: "100%",
    license_key: 'gpl',
    //menubar: false,
    selector: 'textarea.richtext', // Selector de elementos donde se aplicará TinyMCE
    plugins: 'fullpage fullscreen code table visualblocks advlist autolink lists link image charmap print preview anchor',
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
*/


function load_TynyMCE_controls(){

    document.querySelectorAll('textarea.richtext').forEach( cntrol => {
        let h = cntrol.dataset.height;

        if (h && !isNaN(h)) {
            h = parseInt(h);
        }else {
            h=null;
        }

        tinymce.init({
            target: cntrol,
            branding: false,
            height: h || 500,
            width: "100%",
            license_key: 'gpl',
            // 1. Plugins: Elimina 'fullpage' si usas v6/v7 (ya no existe)
            plugins: 'fullpagehtml fullscreen code table visualblocks advlist autolink lists link image charmap preview anchor',
            
            // 2. Permitir etiquetas estructurales (esto es la clave)
            valid_children: '+body[style],+body[script]',
            extended_valid_elements: 'html,head,body,meta[charset|name|content],title,link[rel|href],style,script[src|type]',
            
            // 3. Evitar que TinyMCE "limpie" el HTML inicial
            verify_html: false, 
            
            toolbar: 'undo redo copy cut paste blocks fontfamily fontsize forecolor backcolor | bold italic underline | table bullist numlist outdent indent | alignleft aligncenter alignright alignjustify link unlink image code',
            table_default_attributes: {
                border: 0
            },
            resize: 'both',
            setup: function (editor) {
                editor.on('NodeChange', function (e) {
                    editor.dom.select('table, td, th').forEach(function (el) {
                        el.style.height = '';
                        el.removeAttribute('height');
                    });
                });
            }
        });
    });

    document.querySelectorAll('textarea.richtext_simple').forEach( cntrol => {
        let h = cntrol.dataset.height;

        if (h && !isNaN(h)) {
            h = parseInt(h);
        }else h=null;

        tinymce.init({
            target: cntrol,
            height: h || 200,
            menubar: false,
            statusbar: false,
            branding: false,
            width: "100%",
            license_key: 'gpl',
            // 1. Plugins: Elimina 'fullpage' si usas v6/v7 (ya no existe)
            plugins: 'fullpagehtml fullscreen code table visualblocks advlist autolink lists link image charmap preview anchor',
            
            // 2. Permitir etiquetas estructurales (esto es la clave)
            valid_children: '+body[style],+body[script]',
            extended_valid_elements: 'html,head,body,meta[charset|name|content],title,link[rel|href],style,script[src|type]',
            
            // 3. Evitar que TinyMCE "limpie" el HTML inicial
            verify_html: false, 
            
            toolbar: 'undo redo copy cut paste blocks fontfamily fontsize forecolor backcolor | bold italic underline | table bullist numlist outdent indent | alignleft aligncenter alignright alignjustify link unlink image code',
            table_default_attributes: {
                border: 0
            },
            resize: 'both',
            setup: function (editor) {
                editor.on('NodeChange', function (e) {
                    editor.dom.select('table, td, th').forEach(function (el) {
                        el.style.height = '';
                        el.removeAttribute('height');
                    });
                });
            }
        });
    });
    
}
	//editor de código ACE y demás tipos de controles
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
        load_TynyMCE_controls();

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
