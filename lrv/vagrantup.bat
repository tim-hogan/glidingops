# checkout homestead revision corresponding to versions : * v7.20.0
IF NOT EXIST "vendor/laravel/homestead" (
	git clone https://github.com/laravel/homestead.git vendor/laravel/homestead
	git -C vendor/laravel/homestead checkout cae38adcfdde1de1c4581e7a33872adaf9fbf926
) 

vagrant up