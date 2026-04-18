<?php
// vistas/plantillas/pie_pagina.php
?>
                </div>
                </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                         <span>Copyright &copy; <a href="https://migzam.uk" target="_blank">MigZam</a> <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
            </div>
        </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>

<!-- Plugin de jQuery Easing -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

<!-- Script principal de SB Admin 2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.mi-datatable').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json" },
         "pageLength": 50,
    });

    $('.select-buscador').select2({
            theme: 'bootstrap4', // <--- LA MAGIA VISUAL ESTA AQUI
            width: '100%', 
            language: {
                noResults: function() {
                    return "No se encontraron resultados"; 
                }
            }
        });
});
</script>
</body>
</html>