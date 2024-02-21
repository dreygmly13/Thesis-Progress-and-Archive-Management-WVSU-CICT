<div class="card card-outline rounded-0 card-navy mt-2">
  <div class="card-header">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h4>Category List</h4>
      </div>
      <div class="col-sm-6 d-flex justify-content-end">
        <button type="button" class="btn btn-primary btn-gradient-primary" style="height: 38px;" onclick="handleOpenModal()">Add Category</button>
      </div>
    </div>
  </div>
  <!-- /.card-header -->
  <div class="card-body">
    <table id="category_list" class="table table-bordered table-hover">
      <thead>
        <tr class="bg-gradient-dark text-light">
          <th>Category ID</th>
          <th>Date Created</th>
          <th>Date Updated</th>
          <th>Name</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $query = mysqli_query(
          $conn,
          "SELECT * FROM category_list ORDER BY id ASC"
        );
        while ($category = mysqli_fetch_object($query)) :
        ?>
          <tr>
            <td><?= $category->id ?></td>
            <td><?= date("Y-m-d H:i:s", strtotime($category->date_created)) ?></td>
            <td><?= date("Y-m-d H:i:s", strtotime($category->date_updated)) ?></td>
            <td><?= $category->name ?></td>
            <td class="text-center">
              <button type="button" class="btn btn-warning btn-gradient-warning btn-sm m-1" onclick="handleOpenModal('<?= $category->id ?>')">
                Edit
              </button>
              <button type="button" class="btn btn-danger btn-gradient-danger btn-sm m-1" onclick="handleOnclickDeleteCategory('<?= $category->id ?>')">
                Delete
              </button>
            </td>
          </tr>
          <div class="modal fade" id="editCategory<?= $category->id ?>">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">
                    <i class="fa fa-edit"></i> Update Category Details
                  </h5>
                </div>
                <form method="POST" id="category-form<?= $category->id ?>">
                  <div class="modal-body">
                    <input type="text" name="id" value="<?= $category->id ?>" hidden readonly>
                    <input type="text" name="action" value="edit" hidden readonly>

                    <div class="form-group">
                      <label for="name" class="control-label">Name</label>
                      <input type="text" name="name" class="form-control" value="<?= $category->name ?>" required>
                    </div>
                  </div>
                  <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-primary btn-gradient-primary m-1" onclick="handleSave($(this))">Save</button>
                    <button type="button" class="btn btn-danger btn-gradient-danger m-1" data-dismiss="modal">Cancel</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </tbody>

    </table>
  </div>
  <!-- /.card-body -->
</div>
<div class="modal fade" id="addCategory">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-plus"></i> Add New Category</h5>
      </div>
      <form method="POST" id="category-form">
        <div class="modal-body">
          <input type="text" name="action" value="add" hidden readonly>

          <div class="form-group">
            <label for="name" class="control-label">Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer justify-content-end">
          <button type="button" class="btn btn-primary btn-gradient-primary m-1" onclick="handleSave($(this))">Save</button>
          <button type="button" class="btn btn-danger btn-gradient-danger m-1" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>