<?php


namespace LaravelAssets\Commands;


use Illuminate\Console\Command;
use LaravelAssets\JsService;
use LaravelAssets\LessService;
use LaravelAssets\SvgService;

class CompileCommand extends Command{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'laravelAssets:compile';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Compile Assets';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		LessService::checkFiles();
		JsService::checkFiles();
		SvgService::checkFiles();
	}
}