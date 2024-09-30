// Aguarde o documento HTML estar totalmente carregado
$(document).ready(function(){
  loadBlocksite();
  insertBlocksite();
});

function templateBlocksite(blocksite) {
let blocksiteList = $("#list");

let blocksiteDiv = $("<div>").addClass("row justify-content-center align-items-center");
blocksiteDiv.addClass(`line${blocksite.id_blocksite}`);
blocksiteDiv.css("border-radius", "10px");

// Template para um blocksite
let blocksiteTemplate = `
    <div class="col-9">
      <span style="font-size: 18px; vertical-align: 2px">${blocksite.url}</span>
    </div>
    <div class="col-2">
      <i class="bi bi-pen update" data-id_blocksite="${blocksite.id_blocksite}" style="font-size: 20px; margin-right: 10px;"></i>
      <i class="bi bi-trash3 delete" data-id_blocksite="${blocksite.id_blocksite}" style="font-size: 20px;"></i>
    </div>
`;

// Adicionar o template à div da blocksite
blocksiteDiv.append(blocksiteTemplate);
blocksiteList.append(blocksiteDiv);

// Divisória entre as blocksites
let hr = $("<hr>").css({
    "margin-top": "2px",   // Define a margem superior
    "margin-bottom": "2px", // Define a margem inferior
    "border-color": "rgba(0, 0, 0, 0.6)"
});
hr.addClass(`line${blocksite.id_blocksite}`);
// Adicionar divisória
blocksiteList.append(hr);
};

function templateBlocksiteEdit(blocksiteId) {

  if ($("#editBlocksiteForm").length > 0) {
    alert('Já existe um formulário de edição aberto.');
    return;
  }

  let blocksiteDiv = $("#list").find(`[data-id_blocksite="${blocksiteId}"]`);

  // Encontre a linha (row) que contém a div do updateBlocksite
  let updateBlocksiteRow = blocksiteDiv.closest('.row');

  // Verifique se a div do updateBlocksite e a linha foram encontradas
  if (blocksiteDiv.length === 0 || updateBlocksiteRow.length === 0) {
    alert('Site de bloqueio ou linha não encontrada.');
    return;
  }

  // Crie um formulário de edição
  let editForm = `
  <form id="editBlocksiteForm" data-id_blocksite="${blocksiteId}">
    <div class="row align-items-center">
    <div class="col-7">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-file-earmark-lock"></i></span>
            <input type="text" id="editBlocksiteUrl" name="url" class="form-control form-control-lg fs-6" placeholder="Insira o link de uma página" maxlength="75">
          </div>
    </div>
    <div class="col">
        <button type="submit" id="insertBlocksiteButton" class="btn btn-lg btn-primary btn-gradient fs-6">Adicionar Site<i class="bi bi-plus-lg"></i></button>
    </div>
  </div>
  </form>
  `;

  // Insira o formulário de edição abaixo da div da blocksite
  updateBlocksiteRow.after(editForm);

  // Manipule o evento de envio do formulário
  $("#editBlocksiteForm").submit(function (e) {
    e.preventDefault();
    
    // Obtenha os novos dados do formulário
    let blocksiteId = $(this).data('id_blocksite');
    let newUrl = $("#editBlocksiteUrl").val();

    // Verifique se o campo de título não está vazio
    if (newUrl.trim() === '') {
      alert('O campo de título não pode estar vazio.');
      return;
    }

    // Crie um objeto com os dados a serem enviados
    let formData = {
      id_blocksite: blocksiteId,
      url: newUrl,
    };
  
    // Envie os dados para o controlador PHP usando $.post ou $.ajax
    $.post('/plannerfocus/MVC/Controller/blocksiteController.class.php?command=update', formData, function (data) {
      $("#list").empty();
      loadBlocksite();
  
      // Remova o formulário de edição após a atualização bem-sucedida
      $("#editBlocksiteForm").remove();
    });
  });
}

//CATEGORY = '';
function loadBlocksite() {
$.get('/plannerfocus/MVC/Controller/categoryController.class.php?command=getAll', function (data) {
    let categoryData = JSON.parse(data);
    let selectCategory = document.getElementById('selectCategory');

    selectCategory.innerHTML = '';
    selectCategory.appendChild(new Option('Selecionar Site de bloqueio', ''));

    CATEGORY += '<option selected>Tarefas</option>';

    for (let category of categoryData) {
      selectCategory.appendChild(new Option(category.title, category.id_category));
      CATEGORY +=`<option value="${category.id_category}">${category.title}</option>`;
    }
  }).fail(function (http) {
    if (http.status != 200) {
      document.location = '/';
    }
});

$("#selectCategory").change(function() {
  // Obtenha o ID da categoria selecionada
  let selectedCategoryId = $(this).val();

  // Verifique se uma categoria foi selecionada
  if (selectedCategoryId !== '') {
    // Faça uma solicitação AJAX para obter os blocksites da categoria selecionada
    $.post('/plannerfocus/MVC/Controller/blocksiteController.class.php?command=getAllbyCategory', { id_category: selectedCategoryId }, function(data) {
      // Limpe a lista de blocksites atual
      $("#list").empty();

      // Parse os dados JSON recebidos
      let blocksiteData = JSON.parse(data);

      // Itere sobre os blocksites da categoria selecionada e atualize a lista
      for (let blocksite of blocksiteData) {
        templateBlocksite(blocksite);
      }
      // Carregue funções de deletar e atualizar blocksites
      deleteBlocksite();
      updateBlocksite();
    });
  }
  // Se nenhuma categoria foi selecionada, carregue todos os blocksites
  else {
    $("#list").empty();
    loadBlocksite();
  }
});

};

function insertBlocksite() {
// Executa o comportamento ao clicar no botão
$("#insertBlocksiteButton").on('click', function (e) {
    e.preventDefault(); // Evita comportamento padrão

    // Obtenha o valor do campo de título
    let urlValue = $("#url").val();
    // Obtenha o valor da categoria selecionada
    let selectedCategory = $("#selectCategory").val();

    // Verifique se o campo de título não está vazio
    if (urlValue.trim() === '') {
      alert('O campo de título não pode estar vazio.');
      return;
    }
    if(selectedCategory.trim() === ''){
      alert('Selecione uma categoria para adicionar um link');
      return;
    }

    let blocksite_dataForm = $("#blocksiteForm").serialize(); // Pegar os dados do formulário

    // Add the selected category to the data
    blocksite_dataForm += "&id_category=" + selectedCategory;

    // Post recebe dois parâmetros, uma URL e um objeto (dado) que enviará, também pode fazer um comportamento ao enviar
    $.post('/plannerfocus/MVC/Controller/blocksiteController.class.php?command=insert', blocksite_dataForm, function (data) {
        let blocksite = JSON.parse(data);
        templateBlocksite(blocksite);
        deleteBlocksite();
        updateBlocksite();
    });

    return false; // Evita comportamento padrão
});
};

function updateBlocksite() {
$(".update").off().on('click', function (e) {
  e.preventDefault(); // Evita comportamento padrão  
  let blocksiteId = $(this).data('id_blocksite'); // Pega o id_blocksite da blocksite a ser deletada do atributo de dados

  // Verifique se a div da blocksite foi encontrada
  if (blocksiteId.length === 0) {
    alert('Site de bloqueio não encontrada!');
    return;
  }else{
    templateBlocksiteEdit(blocksiteId);
  }

  return false;
});
}  

function deleteBlocksite() {
// Executa o comportamento ao clicar no botão
$(".delete").off().on('click', function (e) {
    e.preventDefault(); // Evita comportamento padrão  
    let blocksiteId = $(this).data('id_blocksite'); // Pega o id_blocksite da blocksite a ser deletada do atributo de dados
    
    let dataToSend = {
      id_blocksite: blocksiteId
    };

    // Post recebe dois parâmetros, uma URL e um objeto (dado) que enviará, também pode fazer um comportamento ao enviar
    $.post('/plannerfocus/MVC/Controller/blocksiteController.class.php?command=delete', dataToSend, function (data) {
      $(`.line${blocksiteId}`).hide("fast");
      $(`#editBlocksiteForm[data-id_blocksite="${blocksiteId}"]`).remove();
    });
  
    return false; // Evita comportamento padrão
});
};
