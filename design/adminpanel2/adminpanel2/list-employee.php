<?php include 'header.php';?>

<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <ul class="breadcrumb breadcrumb-style ">
                        <li class="breadcrumb-item">
                            <h4 class="page-title">All Employees</h4>
                        </li>
                        <li class="breadcrumb-item bcrumb-1">
                            <a href="../../index.html">
                                <i class="fas fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item bcrumb-2">
                            <a href="#" onclick="return false;">Employee</a>
                        </li>
                        <li class="breadcrumb-item active">All Employees</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="header">
                        <h2>
                            <strong>All</strong> Employees
                        </h2>
                        <ul class="header-dropdown m-r--5">
                            <li class="dropdown">
                                <a href="#" onclick="return false;" class="dropdown-toggle" data-bs-toggle="dropdown"
                                    role="button" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <ul class="dropdown-menu float-end">
                                    <li>
                                        <a href="#" onclick="return false;">Action</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="return false;">Another action</a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="return false;">Something else here</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="body">
                        <div class="table-">
                            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-hover js-basic-example contact_list dataTable"
                                            id="DataTables_Table_0" role="grid"
                                            aria-describedby="DataTables_Table_0_info">
                                            <thead>
                                                <tr role="row">
                                                    <th class="center sorting sorting_asc" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 48.4167px;" aria-sort="ascending"
                                                        aria-label="#: activate to sort column descending">#</th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 126.333px;"
                                                        aria-label=" Name : activate to sort column ascending"> Name
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 149.967px;"
                                                        aria-label=" Designation : activate to sort column ascending">
                                                        Designation </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 141.983px;"
                                                        aria-label=" Mobile : activate to sort column ascending"> Mobile
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 193.017px;"
                                                        aria-label=" Email : activate to sort column ascending"> Email
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 168.017px;"
                                                        aria-label=" Address : activate to sort column ascending">
                                                        Address </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 144.15px;"
                                                        aria-label="Joining Date: activate to sort column ascending">
                                                        Joining Date</th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 85px;"
                                                        aria-label=" Action : activate to sort column ascending"> Action
                                                    </th>
                                                    <th class="center sorting" tabindex="0"
                                                        aria-controls="DataTables_Table_0" rowspan="1" colspan="1"
                                                        style="width: 85px;"
                                                        aria-label=" Action : activate to sort column ascending"> Status
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="gradeX odd">
                                                    <td class="table-img center sorting_1">
                                                        <img src="images/user1.jpg" alt="">
                                                    </td>
                                                    <td class="center">Rajesh</td>
                                                    <td class="center">Programmer</td>
                                                    <td class="center">+ 167-894-2378</td>
                                                    <td class="center">example@email.com</td>
                                                    <td class="center">22,tilak appt. surat</td>
                                                    <td class="center">22 Feb 2000</td>
                                                    <td class="center">
                                                        <a href="edit-employee.html" class="btn btn-tbl-edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <a href="" class="btn btn-tbl-delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="switch mt-3">
                                                            <label>
                                                                <input type="checkbox" class="-change">
                                                                <span class="lever switch-col-red layout-switch"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="gradeX even">
                                                    <td class="table-img center sorting_1">
                                                        <img src="images/user1.jpg" alt="">
                                                    </td>
                                                    <td class="center">Pooja Patel</td>
                                                    <td class="center">Manager</td>
                                                    <td class="center">+ 167-894-2378</td>
                                                    <td class="center">example@email.com</td>
                                                    <td class="center">22,tilak appt. surat</td>
                                                    <td class="center">27 Aug 2005</td>
                                                    <td class="center">
                                                        <a href="edit-employee.html" class="btn btn-tbl-edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <a href="" class="btn btn-tbl-delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="switch mt-3">
                                                            <label>
                                                                <input type="checkbox" class="-change">
                                                                <span class="lever switch-col-red layout-switch"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="gradeX odd">
                                                    <td class="table-img center sorting_1">
                                                        <img src="images/user1.jpg" alt="">
                                                    </td>
                                                    <td class="center">Sarah Smith</td>
                                                    <td class="center">Manager</td>
                                                    <td class="center">+ 167-894-2378</td>
                                                    <td class="center">example@email.com</td>
                                                    <td class="center">22,tilak appt. surat</td>
                                                    <td class="center">05 Jun 2011</td>
                                                    <td class="center">
                                                        <a href="edit-employee.html" class="btn btn-tbl-edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <a href="" class="btn btn-tbl-delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="switch mt-3">
                                                            <label>
                                                                <input type="checkbox" class="-change">
                                                                <span class="lever switch-col-red layout-switch"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="gradeX even">
                                                    <td class="table-img center sorting_1">
                                                        <img src="images/user1.jpg" alt="">
                                                    </td>
                                                    <td class="center">John Deo</td>
                                                    <td class="center">Designer</td>
                                                    <td class="center">+ 167-894-2378</td>
                                                    <td class="center">example@email.com</td>
                                                    <td class="center">22,tilak appt. surat</td>
                                                    <td class="center">15 Feb 2012</td>
                                                    <td class="center">
                                                        <a href="edit-employee.html" class="btn btn-tbl-edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <a href="" class="btn btn-tbl-delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="switch mt-3">
                                                            <label>
                                                                <input type="checkbox" class="-change">
                                                                <span class="lever switch-col-red layout-switch"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="gradeX odd">
                                                    <td class="table-img center sorting_1">
                                                        <img src="images/user1.jpg" alt="">
                                                    </td>
                                                    <td class="center">Jay Soni</td>
                                                    <td class="center">Purchase Officer</td>
                                                    <td class="center">+ 167-894-2378</td>
                                                    <td class="center">example@email.com</td>
                                                    <td class="center">22,tilak appt. surat</td>
                                                    <td class="center">12 Nov 2012</td>
                                                    <td class="center">
                                                        <a href="edit-employee.html" class="btn btn-tbl-edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <a href="" class="btn btn-tbl-delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="switch mt-3">
                                                            <label>
                                                                <input type="checkbox" class="-change">
                                                                <span class="lever switch-col-red layout-switch"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="gradeX even">
                                                    <td class="table-img center sorting_1">
                                                        <img src="images/user1.jpg" alt="">
                                                    </td>
                                                    <td class="center">Jacob Ryan</td>
                                                    <td class="center">Receptionist</td>
                                                    <td class="center">+ 167-894-2378</td>
                                                    <td class="center">example@email.com</td>
                                                    <td class="center">22,tilak appt. surat</td>
                                                    <td class="center">03 Dec 2001</td>
                                                    <td class="center">
                                                        <a href="edit-employee.html" class="btn btn-tbl-edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <a href="" class="btn btn-tbl-delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="switch mt-3">
                                                            <label>
                                                                <input type="checkbox" class="-change">
                                                                <span class="lever switch-col-red layout-switch"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="gradeX odd">
                                                    <td class="table-img center sorting_1">
                                                        <img src="images/user1.jpg" alt="">
                                                    </td>
                                                    <td class="center">Megha Trivedi</td>
                                                    <td class="center">Manager</td>
                                                    <td class="center">+ 167-894-2378</td>
                                                    <td class="center">example@email.com</td>
                                                    <td class="center">22,tilak appt. surat</td>
                                                    <td class="center">17 Mar 2013</td>
                                                    <td class="center">
                                                        <a href="edit-employee.html" class="btn btn-tbl-edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <a href="" class="btn btn-tbl-delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="switch mt-3">
                                                            <label>
                                                                <input type="checkbox" class="-change">
                                                                <span class="lever switch-col-red layout-switch"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="gradeX even">
                                                    <td class="table-img center sorting_1">
                                                        <img src="images/user1.jpg" alt="">
                                                    </td>
                                                    <td class="center">Rajesh</td>
                                                    <td class="center">Sr. Tester</td>
                                                    <td class="center">+ 167-894-2378</td>
                                                    <td class="center">example@email.com</td>
                                                    <td class="center">22,tilak appt. surat</td>
                                                    <td class="center">22 Feb 2000</td>
                                                    <td class="center">
                                                        <a href="edit-employee.html" class="btn btn-tbl-edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <a href="" class="btn btn-tbl-delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="switch mt-3">
                                                            <label>
                                                                <input type="checkbox" class="-change">
                                                                <span class="lever switch-col-red layout-switch"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="gradeX odd">
                                                    <td class="table-img center sorting_1">
                                                        <img src="images/user1.jpg" alt="">
                                                    </td>
                                                    <td class="center">Pooja Patel</td>
                                                    <td class="center">Team Leader</td>
                                                    <td class="center">+ 167-894-2378</td>
                                                    <td class="center">example@email.com</td>
                                                    <td class="center">22,tilak appt. surat</td>
                                                    <td class="center">27 Aug 2005</td>
                                                    <td class="center">
                                                        <a href="edit-employee.html" class="btn btn-tbl-edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <a href="" class="btn btn-tbl-delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="switch mt-3">
                                                            <label>
                                                                <input type="checkbox" class="-change">
                                                                <span class="lever switch-col-red layout-switch"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="gradeX even">
                                                    <td class="table-img center sorting_1">
                                                        <img src="images/user1.jpg" alt="">
                                                    </td>
                                                    <td class="center">Sarah Smith</td>
                                                    <td class="center">Manager</td>
                                                    <td class="center">+ 167-894-2378</td>
                                                    <td class="center">example@email.com</td>
                                                    <td class="center">22,tilak appt. surat</td>
                                                    <td class="center">05 Jun 2011</td>
                                                    <td class="center">
                                                        <a href="edit-employee.html" class="btn btn-tbl-edit">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <a href="" class="btn btn-tbl-delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="switch mt-3">
                                                            <label>
                                                                <input type="checkbox" class="-change">
                                                                <span class="lever switch-col-red layout-switch"></span>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="center" rowspan="1" colspan="1">#</th>
                                                    <th class="center" rowspan="1" colspan="1"> Name </th>
                                                    <th class="center" rowspan="1" colspan="1"> Designation </th>
                                                    <th class="center" rowspan="1" colspan="1"> Mobile </th>
                                                    <th class="center" rowspan="1" colspan="1"> Email </th>
                                                    <th class="center" rowspan="1" colspan="1"> Address </th>
                                                    <th class="center" rowspan="1" colspan="1">Joining Date</th>
                                                    <th class="center" rowspan="1" colspan="1"> Action </th>
                                                    <th class="center" rowspan="1" colspan="1">Status</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php';?>