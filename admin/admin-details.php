<?php include '../partials/admin-header.php'; ?>

    <main class="p-2 px-4">
        <section id="admin-details">
            <div class="content p-4">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6">
                        <h1 class="fw-bold mb-0">Edit Profile</h1>
                    </div>
                </div>

                <form action="" method="post">
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first-name" class="form-label fw-bold">First Name</label>
                                    <input type="text" class="form-control" id="first-name" value="John">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last-name" class="form-label fw-bold">Last Name</label>
                                    <input type="text" class="form-control" id="last-name" value="Doe">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="admin-email" class="form-label fw-bold">Email</label>
                                    <input type="email" class="form-control" id="admin-email" value="admin@example.com">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="admin-phone" class="form-label fw-bold">Phone</label>
                                    <input type="tel" class="form-control" id="admin-phone" value="">
                                </div>
                            </div>

                            <hr class="my-4">
<!--This is a property of PLSP-CCST BSIT-3B SY 2024-2025 -->

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label fw-bold">Password</label>
                                    <input type="password" class="form-control" id="password" value="">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password-confirmation" class="form-label fw-bold">Password Confirmation</label>
                                    <input type="password" class="form-control" id="password-confirmation" value="">
                                </div>
                            </div>

                            <hr class="mt-4">
                        </div>

                        <div class="col-md-8 text-end">
                            <button type="button" class="btn btn-dark" data-bs-toggle="modal"
                                data-bs-target="#confirm-modal">Update</button>
                        </div>
                    </div>
                </form>
            </div>    
        </section>
    </main>

    <!-- Edit Confirmation Modal -->
    <div class="custom-modal modal fade" id="confirm-modal" tabindex="-1" aria-labelledby="confirmModalLabel"
        aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header justify-content-center pb-1" style="border-bottom: none;">
                    <h5 class="modal-title text-success text-center fs-3 fw-light" id="confirmModalLabel">CONFIRM CHANGES</h5>
                </div>
                <div class="modal-body text-center">
                    Are you sure you want to update your profile details?
                </div>
                <div class="modal-footer justify-content-center text-center pt-1" style="border-top: none;">
                    <form action="" method="post">
                        <button type="submit" class="btn btn-success me-3" id="confirmEdit">Save</button>
                        <button type="button" class="btn btn-danger ms-3" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php include '../partials/admin-footer.php';?>