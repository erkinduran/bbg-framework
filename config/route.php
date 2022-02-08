<?php
    return [
        "/" => [\App\Front\Home::class,"index"],
        "/calc" => [\App\Front\Home::class,"calc"],
        "/getCalculations" => [\App\Front\Home::class,"getCalculations"],
    ];