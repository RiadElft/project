<?php include_once 'views/admin/partials/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $title ?></h1>
        <a href="/admin/cruises" class="btn btn-secondary">Back to Cruises</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="/admin/cruises/store" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Cruise Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="departure_port">Departure Port</label>
                            <input type="text" class="form-control" id="departure_port" name="departure_port" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="departure_date">Departure Date</label>
                            <input type="date" class="form-control" id="departure_date" name="departure_date" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="return_date">Return Date</label>
                            <input type="date" class="form-control" id="return_date" name="return_date" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="price">Price per Person</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="capacity">Capacity</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="destination_ports">Destination Ports</label>
                            <textarea class="form-control" id="destination_ports" name="destination_ports" rows="3" placeholder="Enter ports, one per line"></textarea>
                            <small class="form-text text-muted">Enter each port on a new line</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="amenities">Amenities</label>
                            <textarea class="form-control" id="amenities" name="amenities" rows="3" placeholder="Enter amenities, one per line"></textarea>
                            <small class="form-text text-muted">Enter each amenity on a new line</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="ship_details">Ship Details</label>
                    <textarea class="form-control" id="ship_details" name="ship_details" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Cruise Image</label>
                    <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                    <small class="form-text text-muted">Recommended size: 800x600 pixels</small>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Create Cruise</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once 'views/admin/partials/footer.php'; ?> 