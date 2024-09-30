// Aguarde o documento HTML estar totalmente carregado
$(document).ready(function(){
    loadUsernameInfo();
    updatePicture();
    updateUsername();
    updatePassword();
});

function animationMessage(warning, message, color){
  warning.show();
  warning.text(message);
  warning.css('color', color);
  warning.css('opacity', '1');

  setTimeout(function () {
    warning.css('opacity', '0');
    setTimeout(function () {
      warning.hide();
    }, 2000);
  }, 3000);
}

function loadUsernameInfo(){
    $.ajax({
        type: 'GET',
        url: '/plannerfocus/MVC/Controller/userController.class.php?command=getOne',
        dataType: 'json',
        success: function(data) {
          if (data.username && $("#profile-username").text() !== data.username) {
            $('#profile-username').text(data.username);
            $('#username').text(data.username);
          }
          if (data.email && $("#profile-email").text() !== data.email) {
              $('#profile-email').text(data.email);
          }
          if (data.picture && $("#profile-picture").attr("src") !== data.picture) {
              $('#profile-picture').attr('src', data.picture);
              $('#picture').attr('src', data.picture);
          }
        }
      });
}

function updatePicture() {
  // Executa o comportamento ao clicar no botão
  $("#profilePictureButton").on('click', function (e) {
    e.preventDefault(); // Evita comportamento padrão

    let pictureInput = $("#profilePicture")[0]; // Obtenha o input de arquivo
    if (!pictureInput.files || pictureInput.files.length === 0) {
      alert('Adicione um arquivo para editar a imagem de perfil!');
      return;
    }

    // Verifique o tipo de arquivo
    const allowedExtensions = ['.jpeg', '.jpg', '.png'];
    const fileName = pictureInput.files[0].name;
    const fileExtension = fileName.slice(((fileName.lastIndexOf(".") - 1) >>> 0) + 2);

    if (!allowedExtensions.includes(`.${fileExtension}`)) {
      alert('Apenas arquivos .jpeg, .jpg e .png são permitidos.');
      return;
    }

    let formData = new FormData(); // Crie um objeto FormData

    // Adicione o arquivo ao objeto FormData
    formData.append('profilePicture', pictureInput.files[0]);

    // Agora, você pode enviar o FormData via AJAX
    $.ajax({
      url: '/plannerfocus/MVC/Controller/userController.class.php?command=updatePicture', // Substitua com a URL apropriada
      type: 'POST',
      data: formData,
      processData: false, // Evita que o jQuery processe os dados
      contentType: false, // Define o cabeçalho 'Content-Type' como 'multipart/form-data'
      success: function () {
          loadUsernameInfo();
      },
      error: function () {
        // Lida com erros de requisição
        alert("Erro ao atualizar foto de perfil!");
      }
    });
  });
};

// Função para atualizar um usuario
function updateUsername() {
   // Executa o comportamento ao clicar no botão
   $("#profileUsernameButton").on('click', function (e) {
      e.preventDefault(); // Evita comportamento padrão

      let usernameValue = $("#profileUsername").val();
      if (usernameValue.trim() === '') {
          alert('Adicione um nome para editar o nome do perfil!');
          return;
      }

      let userUsername_dataForm = $("#profileUsernameForm").serialize(); // Pegar os dados do formulário
      // Post recebe dois parâmetros, uma URL e um objeto (dado) que enviará, também pode fazer um comportamento ao enviar
      $.post('/plannerfocus/MVC/Controller/userController.class.php?command=updateUsername', userUsername_dataForm, function (data) {
        const warningUsername = $('#warningUsername');
        animationMessage(warningUsername, "Nome de Perfil atualizado com sucesso!", "limegreen");
        loadUsernameInfo();
      });

      return false; // Evita comportamento padrão
  });
};

// Função para atualizar um usuario
function updatePassword() {
  // Executa o comportamento ao clicar no botão
  $("#profilePasswordButton").on('click', function (e) {
     e.preventDefault(); // Evita comportamento padrão

     let oldPasswordValue = $("#oldpassword").val();
     let newPasswordValue = $("#newpassword").val();
     let passwordRepeatValue = $("#passwordrepeat").val();

     if (oldPasswordValue.trim() === '' || newPasswordValue.trim() === '' || passwordRepeatValue.trim() === '') {
        const warningPassword = $('#warningPassword');
        animationMessage(warningPassword, "Campos estão nulos! Confira novamente!", "red");
        return;
     }

     if(newPasswordValue.trim() !== passwordRepeatValue.trim()){
        const warningPassword = $('#warningPassword');
        animationMessage(warningPassword, "As senhas não coincidem!", "red");
        return;
     }

     let userPassword_dataForm = $("#profilePasswordForm").serialize(); // Pegar os dados do formulário
     // Post recebe dois parâmetros, uma URL e um objeto (dado) que enviará, também pode fazer um comportamento ao enviar
     $.post('/plannerfocus/MVC/Controller/userController.class.php?command=updatePassword', userPassword_dataForm, function (response) {
      if (response.error) {
        const warningPassword = $('#warningPassword');
        animationMessage(warningPassword, response.error, "red");
        return;
      } else {
        const warningPassword = $('#warningPassword');
        animationMessage(warningPassword, "Senha atualizada com sucesso!", "limegreen");
     }
    }, 'json');

     return false; // Evita comportamento padrão
 });
};