// Aguarde o documento HTML estar totalmente carregado
$(document).ready(function(){
    loadCategory();
    insertCategory();
});


// Função para criar um template de uma categoria
function templateCategory(category) {
  let categoryList = $("#list");

  let categoryDiv = $("<div>").addClass("row justify-content-center align-items-center");
  categoryDiv.addClass(`line${category.id_category}`);
  categoryDiv.css("border-radius", "10px");

  // Template para uma categoria
  let categoryTemplate = `
      <div class="col-9">
        <i class="bi bi-bookmark-fill" style="color: ${category.color}; font-size: 24px; margin-right: 20px;"></i>
        <span style="font-size: 18px; vertical-align: 2px">${category.title}</span>
      </div>
      <div class="col-2">
        <i class="bi bi-pen update" data-id_category="${category.id_category}" style="font-size: 20px; margin-right: 10px;"></i>
        <i class="bi bi-trash3 delete" data-id_category="${category.id_category}" style="font-size: 20px;"></i>
      </div>
  `;

  // Adicionar o template à div da categoria
  categoryDiv.append(categoryTemplate);
  categoryList.append(categoryDiv);

  // Divisória entre as categories
  let hr = $("<hr>").css({
      "margin-top": "2px",   // Define a margem superior
      "margin-bottom": "2px", // Define a margem inferior
      "border-color": "rgba(0, 0, 0, 0.6)"
  });
  hr.addClass(`line${category.id_category}`);
  // Adicionar divisória
  categoryList.append(hr);
};

// Função para criar um template para editar uma categoria
function templateCategoryEdit(categoryId) {

    if ($("#editCategoryForm").length > 0) {
      alert('Já existe um formulário de edição aberto.');
      return;
    }

    let categoryDiv = $("#list").find(`[data-id_category="${categoryId}"]`);

    // Encontre a linha (row) que contém a div do updateCategory
    let updateCategoryRow = categoryDiv.closest('.row');

    // Verifique se a div do updateCategory e a linha foram encontradas
    if (categoryDiv.length === 0 || updateCategoryRow.length === 0) {
      alert('Categoria não encontrada.');
      return;
    }

    // Crie um formulário de edição
    let editForm = `
    <form id="editCategoryForm" data-id_category="${categoryId}">
      <div class="row justify-content-between align-items-center">
        <div class="col-5">
          <div class="input-group" style="margin-left:10%">
            <span class="input-group-text"><i class="bi bi-bookmark"></i></span>
            <input type="text" id="editCategoryTitle" name="title" class="form-control form-control-lg fs-6" placeholder="Dê um título para sua Categoria" maxlength="30" style="max-width: 80% ">
          </div>
        </div>
        <div class="col-4">
          <div class="input-group">
            <input type="color" id="editCategoryColor" name="color" class="form-control form-control-color fs-6" value="#3493fa" required style="max-width: 40px;">
            <span class="input-group-text text-muted">Dê uma cor para a Categoria</span>
          </div>
        </div>
        <div class="col text-center">
          <button type="submit" id="insertCategoryButton" class="btn btn-lg btn-primary btn-gradient fs-6">Editar Categoria <i class="bi bi-pen-fill"></i></i></button>
        </div>
      </div>
    </form>
    `;

    // Insira o formulário de edição abaixo da div da categoria
    updateCategoryRow.after(editForm);

    // Manipule o evento de envio do formulário
    $("#editCategoryForm").submit(function (e) {
      e.preventDefault();
      
      // Obtenha os novos dados do formulário
      let categoryId = $(this).data('id_category');
      let newTitle = $("#editCategoryTitle").val();
      let newColor = $("#editCategoryColor").val();

      // Verifique se o campo de título não está vazio
      if (newTitle.trim() === '') {
        alert('O campo de título não pode estar vazio.');
        return;
      }

      // Crie um objeto com os dados a serem enviados
      let formData = {
        id_category: categoryId,
        title: newTitle,
        color: newColor
      };
    
      // Envie os dados para o controlador PHP usando $.post ou $.ajax
      $.post('/plannerfocus/MVC/Controller/categoryController.class.php?command=update', formData, function (data) {
        $("#list").empty();
        loadCategory();
    
        // Remova o formulário de edição após a atualização bem-sucedida
        $("#editCategoryForm").remove();
      });
    });
}

// Função para ler todas as categories
function loadCategory() {
  
  let response = $.get('/plannerfocus/MVC/Controller/categoryController.class.php?command=getAll', function (data) {
      let categoryData = JSON.parse(data);
      
      for (let category of categoryData) {
          templateCategory(category);
      }
      // Carregar função de deletar
      deleteCategory();
      updateCategory();
  });

  response.fail(function(http){
    if (http.status != 200){
      document.location = '/index.html';
    }
  });
};

// Função para inserir uma nova category
function insertCategory() {
  // Executa o comportamento ao clicar no botão
  $("#insertCategoryButton").on('click', function (e) {
      e.preventDefault(); // Evita comportamento padrão

      // Obtenha o valor do campo de título
      let titleValue = $("#title").val();

      // Verifique se o campo de título não está vazio
      if (titleValue.trim() === '') {
        alert('O campo de título não pode estar vazio.');
        return;
      }

      let category_dataForm = $("#categoryForm").serialize(); // Pegar os dados do formulário

      // Post recebe dois parâmetros, uma URL e um objeto (dado) que enviará, também pode fazer um comportamento ao enviar
      $.post('/plannerfocus/MVC/Controller/categoryController.class.php?command=insert', category_dataForm, function (data) {
          let category = JSON.parse(data);
          templateCategory(category);
          deleteCategory();
          updateCategory();
      });

      return false; // Evita comportamento padrão
  });
};

function updateCategory() {
  $(".update").off().on('click', function (e) {
    e.preventDefault(); // Evita comportamento padrão  
    let categoryId = $(this).data('id_category'); // Pega o id_category da etiquta a ser deletada do atributo de dados

    // Verifique se a div da categoria foi encontrada
    if (categoryId.length === 0) {
      alert('Categoria não encontrada!');
      return;
    }else{
      templateCategoryEdit(categoryId);
    }

    return false;
  });
}  

// Função para inserir uma nova category
function deleteCategory() {
  // Executa o comportamento ao clicar no botão
  $(".delete").off().on('click', function (e) {
      e.preventDefault(); // Evita comportamento padrão  
      let categoryId = $(this).data('id_category'); // Pega o id_category da etiquta a ser deletada do atributo de dados
      
      let dataToSend = {
        id_category: categoryId
      };

      // Post recebe dois parâmetros, uma URL e um objeto (dado) que enviará, também pode fazer um comportamento ao enviar
      $.post('/plannerfocus/MVC/Controller/categoryController.class.php?command=delete', dataToSend, function (data) {
        $(`.line${categoryId}`).hide("fast");
        $(`#editCategoryForm[data-id_category="${categoryId}"]`).remove();
      });
    
      return false; // Evita comportamento padrão
  });
};
