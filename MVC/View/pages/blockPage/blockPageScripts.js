$(document).ready(function(){
    const urlParams = new URLSearchParams(window.location.search);
    const id_user = urlParams.get('id_user');
    getUser(id_user);
    createModal();
    contentBlockpage(id_user);
    updateBlockpage(id_user);
});
    
// Função para exibir nome e foto
function getUser(token) {
    $.get('/plannerfocus/MVC/Controller/blockpageController.class.php?command=userBlockpage&id_user=' + token, function (data) {
        let blockpageUserData = JSON.parse(data);
        if (blockpageUserData.username) {
            $('#username').text(blockpageUserData.username);
        }
    })
};

function createModal(){
    // Quando o botão "Editar" é clicado
    $('.btn-brush').click(function() {
        // Abra o modal de edição
        $('#editModal').modal('show');
    });
};

function contentBlockpage(token){
    $.get('/plannerfocus/MVC/Controller/blockpageController.class.php?command=getOne&id_user=' + token, function (data) {
        let blockpageData = JSON.parse(data);
        if (blockpageData.title && $("#titleBlockpage").text() !== blockpageData.title) {
            $('#titleBlockpage').text(blockpageData.title);
        }
        if (blockpageData.description && $("#descriptionBlockpage").text() !== blockpageData.description) {
            $('#descriptionBlockpage').text(blockpageData.description);
        }
        if (blockpageData.background) {
            const body = $('body');
            body.css('background-image', 'url("../../' + blockpageData.background + '")');
        }
    });
};

function updateBlockpage(token){
    $("#editBlockpageButton").on('click', function (e) {
        e.preventDefault();
        // Obtenha os novos dados do formulário
        var newTitle = $('#title').val();
        var newDescription = $('#description').val();
    
        // Verifique se o campo de título não está vazio
        if (newTitle.trim() === '') {
          alert('O campo de título não pode estar vazio.');
          return;
        }
    
        var formData = new FormData();
        formData.append('id_user', token);
        formData.append('title', newTitle);
        formData.append('description', newDescription);
        
        let backgroundInput = $("#background")[0]; // Obtenha o input de arquivo
        if (!backgroundInput.files || backgroundInput.files.length === 0) {
          alert('Adicione um arquivo para editar a imagem de perfil!');
          return;
        }
    
        // Verifique o tipo de arquivo
        const allowedExtensions = ['.jpeg', '.jpg', '.png'];
        const fileName = backgroundInput.files[0].name;
        const fileExtension = fileName.slice(((fileName.lastIndexOf(".") - 1) >>> 0) + 2);
    
        if (!allowedExtensions.includes(`.${fileExtension}`)) {
          alert('Apenas arquivos .jpeg, .jpg e .png são permitidos.');
          return;
        }
    
        // Adicione o arquivo ao objeto FormData
        formData.append('background', backgroundInput.files[0]);
        // Envie os dados para o controlador PHP usando $.post ou $.ajax
        $.ajax({
            url: '/plannerfocus/MVC/Controller/blockpageController.class.php?command=update',
            type: 'POST',
            data: formData,
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Prevent jQuery from setting content type
            success: function (data) {
                $('#editModal').modal('hide');
                let blockpageData = JSON.parse(data);
                if (blockpageData.title && $("#titleBlockpage").text() !== blockpageData.title) {
                    $('#titleBlockpage').text(blockpageData.title);
                }
                if ($("#descriptionBlockpage").text() !== blockpageData.description) {
                    $('#descriptionBlockpage').text(blockpageData.description);
                }
                if (blockpageData.background) {
                    const body = $('body');
                    body.css('background-image', 'url("../../' + blockpageData.background + '")');
                }
            }
        });
    });
};