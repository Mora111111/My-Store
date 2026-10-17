<?php
class VisitorGeoApiController {
    public function store(): void {
        Session::updatePreciseLocation();
    }
}
