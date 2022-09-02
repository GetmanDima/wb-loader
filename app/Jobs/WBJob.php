<?php

namespace App\Jobs;

use App\Models\ExciseGood;
use App\Models\Income;
use App\Models\Order;
use App\Models\Report;
use App\Models\Sale;
use App\Models\Stock;
use DateTimeInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class WBJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const RFC_TIME_FORMAT = "Y-m-d\TH:i:s.u";
    private ?string $apiUrl;
    private ?string $apiKey;
    private Carbon $defaultDateFrom;
    private LoggerInterface $log;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->apiUrl = env("API_URL");
       $this->apiKey = env("API_KEY");
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->defaultDateFrom = (new Carbon())->subYear();
        $this->log = Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/wb.log'),
        ]);

        $this->log->info("Start integration");

        $this->handleIncomes();
        $this->handleStocks();
        $this->handleOrders();
        $this->handleSales();
        $this->handleReports();
        $this->handleExciseGoods();

        $this->log->info("Finish integration");
    }

    private function handleIncomes()
    {
        try {
            $rfcDateFrom = $this->dateTimeOrDefaultToRfc($this->getIncomesDateFrom());
            $incomes = $this->fetchIncomes($rfcDateFrom);

            if (count($incomes) === 0) {
                $this->log->info("New incomes not found. Date from: $rfcDateFrom");
            } else {
                $this->insertIncomes($incomes);
            }
        } catch (QueryException $e) {
            $this->logQueryError($e);
        } catch (Exception $e) {
            $this->log->error($e->getMessage());
        }
    }

    private function handleStocks()
    {
        try {
            $rfcDateFrom = $this->dateTimeOrDefaultToRfc($this->getStocksDateFrom());
            $stocks = $this->fetchStocks($rfcDateFrom);

            if (count($stocks) === 0) {
                $this->log->info("New stocks not found. Date from: $rfcDateFrom");
            } else {
                $this->insertStocks($stocks);
            }
        } catch (QueryException $e) {
            $this->logQueryError($e);
        } catch (Exception $e) {
            $this->log->error($e->getMessage());
        }
    }

    private function handleOrders()
    {
        try {
            $rfcDateFrom = $this->dateTimeOrDefaultToRfc($this->getOrdersDateFrom());
            $orders = $this->fetchOrders($rfcDateFrom);

            if (count($orders) === 0) {
                $this->log->info("New orders not found. Date from: $rfcDateFrom");
            } else {
                $this->insertOrders($orders);
            }
        } catch (QueryException $e) {
            $this->logQueryError($e);
        } catch (Exception $e) {
            $this->log->error($e->getMessage());
        }
    }

    private function handleSales()
    {
        try {
            $rfcDateFrom = $this->dateTimeOrDefaultToRfc($this->getSalesDateFrom());
            $sales = $this->fetchSales($rfcDateFrom);

            if (count($sales) === 0) {
                $this->log->info("New sales not found. Date from: $rfcDateFrom");
            } else {
                $this->insertSales($sales);
            }
        } catch (QueryException $e) {
            $this->logQueryError($e);
        } catch (Exception $e) {
            $this->log->error($e->getMessage());
        }
    }

    private function handleExciseGoods()
    {
        try {
            $rfcDateFrom = $this->dateTimeOrDefaultToRfc($this->getExciseGoodsDateFrom());
            $exciseGoods = $this->fetchExciseGoods($rfcDateFrom);

            if (count($exciseGoods) === 0) {
                $this->log->info("New excise goods not found. Date from: $rfcDateFrom");
            } else {
                $this->insertExciseGoods($exciseGoods);
            }
        } catch (QueryException $e) {
            $this->logQueryError($e);
        } catch (Exception $e) {
            $this->log->error($e->getMessage());
        }
    }

    private function handleReports()
    {
        try {
            $rfcDateFrom = $this->dateTimeOrDefaultToRfc($this->getReportsDateFrom());
            $reports = $this->fetchReports($rfcDateFrom, $this->dateTimeToRfc(new Carbon()));

            if (count($reports) === 0) {
                $this->log->info("New reports not found. Date from: $rfcDateFrom");
            } else {
                $this->insertReports($reports);
            }
        } catch (QueryException $e) {
            $this->logQueryError($e);
        } catch (Exception $e) {
            $this->log->error($e->getMessage());
        }
    }

    /**
     * @throws QueryException
     */
    private function getIncomesDateFrom(): ?Carbon
    {
        $lastChangeDate = Income::max('lastChangeDate');
        return is_null($lastChangeDate) ? null : new Carbon($lastChangeDate);
    }

    /**
     * @throws Exception
     */
    private function fetchIncomes($rfcDateFrom): array
    {
        $response = Http::get(
            $this->getFullUrlWithKey('/v1/supplier/incomes') .
            "&dateFrom=$rfcDateFrom"
        );

        if ($response->status() !== 200) {
            throw new Exception("Fetch incomes error. Date from: $rfcDateFrom. Response status: " . $response->status());
        }

        return $response->json();
    }

    /**
     * @throws QueryException
     */
    private function insertIncomes($incomes)
    {
        $data = [];

        foreach ($incomes as $income) {
            $data[] = [
                'incomeid' => $income['incomeid'],
                'Number' => $income['Number'],
                'date' => $income['date'],
                'lastChangeDate' => $income['lastChangeDate'],
                'supplierArticle' => $income['supplierArticle'],
                'techSize' => $income['techSize'],
                'barcode' => $income['barcode'],
                'quantity' => $income['quantity'],
                'totalPrice' => $income['totalPrice'],
                'dateClose' => $income['dateClose'],
                'warehouseName' => $income['warehouseName'],
                'nmid' => $income['nmid'],
                'status' => $income['status'],
            ];
        }

        Income::insert($data);
    }

    /**
     * @throws QueryException
     */
    private function getStocksDateFrom(): ?Carbon
    {
        $lastChangeDate = Stock::max('lastChangeDate');
        return is_null($lastChangeDate) ? null : new Carbon($lastChangeDate);
    }

    /**
     * @throws Exception
     */
    private function fetchStocks($rfcDateFrom): array
    {
        $response = Http::get(
            $this->getFullUrlWithKey('/v1/supplier/stocks') .
            "&dateFrom=$rfcDateFrom"
        );

        if ($response->status() !== 200) {
            throw new Exception("Fetch stocks error. Date from: $rfcDateFrom. Response status: " . $response->status());
        }

        return $response->json();
    }

    /**
     * @throws QueryException
     */
    private function insertStocks($stocks)
    {
        $data = [];

        foreach ($stocks as $stock) {
            $data[] = [
                'lastChangeDate' => $stock['lastChangeDate'],
                'supplierArticle' => $stock['supplierArticle'],
                'techSize' => $stock['techSize'],
                'barcode' => $stock['barcode'],
                'quantity' => $stock['quantity'],
                'isSupply' => $stock['isSupply'],
                'isRealization' => $stock['isRealization'],
                'quantityFull' => $stock['quantityFull'],
                'quantityNotInOrders' => $stock['quantityNotInOrders'],
                'warehouseName' => $stock['warehouseName'],
                'inWayToClient' => $stock['inWayToClient'],
                'inWayFromClient' => $stock['inWayFromClient'],
                'nmId' => $stock['nmId'],
                'subject' => $stock['subject'],
                'category' => $stock['category'],
                'daysOnSite' => $stock['daysOnSite'],
                'brand' => $stock['brand'],
                'SCCode' => $stock['SCCode'],
                'warehouse' => $stock['warehouse'],
                'Price' => $stock['Price'],
                'Discount' => $stock['Discount'],
            ];
        }

        Stock::insert($data, ['nmId', 'lastChangeDate']);
    }

    /**
     * @throws QueryException
     */
    private function getOrdersDateFrom(): ?Carbon
    {
        $lastChangeDate = Order::max('lastChangeDate');
        return is_null($lastChangeDate) ? null : new Carbon($lastChangeDate);
    }

    /**
     * @throws Exception
     */
    private function fetchOrders($rfcDateFrom, $flag = 0): array
    {
        $response = Http::get(
            $this->getFullUrlWithKey('/v1/supplier/orders') .
            "&flag=$flag&dateFrom=$rfcDateFrom"
        );

        if ($response->status() !== 200) {
            throw new Exception("Fetch orders error. Date from: $rfcDateFrom. Response status: " . $response->status());
        }

        return $response->json();
    }

    /**
     * @throws QueryException
     */
    private function insertOrders($orders)
    {
        $data = [];

        foreach ($orders as $order) {
            $data[] = [
                'date' => $order['date'],
                'lastChangeDate' => $order['lastChangeDate'],
                'supplierArticle' => $order['supplierArticle'],
                'techSize' => $order['techSize'],
                'barcode' => $order['barcode'],
                'totalPrice' => $order['totalPrice'],
                'discountPercent' => $order['discountPercent'],
                'warehouseName' => $order['warehouseName'],
                'oblast' => $order['oblast'],
                'incomeID' => $order['incomeID'],
                'odid' => $order['odid'],
                'nmId' => $order['nmId'],
                'subject' => $order['subject'],
                'category' => $order['category'],
                'brand' => $order['brand'],
                'isCancel' => $order['isCancel'],
                'cancel_dt' => $order['cancel_dt'],
                'gNumber' => $order['gNumber'],
                'sticker' => $order['sticker'],
                'srid' => $order['srid'],
            ];
        }

        Order::insert($data);
    }

    /**
     * @throws QueryException
     */
    private function getSalesDateFrom(): ?Carbon
    {
        $lastChangeDate = Sale::max('lastChangeDate');
        return is_null($lastChangeDate) ? null : new Carbon($lastChangeDate);
    }

    /**
     * @throws Exception
     */
    private function fetchSales($rfcDateFrom, $flag = 0): array
    {
        $response = Http::get(
            $this->getFullUrlWithKey('/v1/supplier/sales') .
            "&flag=$flag&dateFrom=$rfcDateFrom"
        );

        if ($response->status() !== 200) {
            throw new Exception("Fetch sales error. Date from: $rfcDateFrom. Response status: " . $response->status());
        }

        return $response->json();
    }

    /**
     * @throws QueryException
     */
    private function insertSales($sales)
    {
        $data = [];

        foreach ($sales as $sale) {
            $data[] = [
                'date' => $sale['date'],
                'lastChangeDate' => $sale['lastChangeDate'],
                'supplierArticle' => $sale['supplierArticle'],
                'techSize' => $sale['techSize'],
                'barcode' => $sale['barcode'],
                'totalPrice' => $sale['totalPrice'],
                'discountPercent' => $sale['discountPercent'],
                'isSupply' => $sale['isSupply'],
                'isRealization' => $sale['isRealization'],
                'promoCodeDiscount' => $sale['promoCodeDiscount'],
                'warehouseName' => $sale['warehouseName'],
                'countryName' => $sale['countryName'],
                'oblastOkrugName' => $sale['oblastOkrugName'],
                'regionName' => $sale['regionName'],
                'incomeID' => $sale['incomeID'],
                'saleID' => $sale['saleID'],
                'odid' => $sale['odid'],
                'spp' => $sale['spp'],
                'forPay' => $sale['forPay'],
                'finishedPrice' => $sale['finishedPrice'],
                'priceWithDisc' => $sale['priceWithDisc'],
                'nmId' => $sale['nmId'],
                'subject' => $sale['subject'],
                'category' => $sale['category'],
                'brand' => $sale['brand'],
                'IsStorno' => $sale['IsStorno'],
                'gNumber' => $sale['gNumber'],
                'sticker' => $sale['sticker'],
                'srid' => $sale['srid'],
            ];
        }

        Sale::insert($data);
    }

    /**
     * @throws QueryException
     */
    private function getReportsDateFrom(): ?Carbon
    {
        $lastChangeDate = Report::max('date_to');
        return is_null($lastChangeDate) ? null : (new Carbon($lastChangeDate))->addDay();
    }

    /**
     * @throws Exception
     */
    private function fetchReports($rfcDateFrom, $rfcDateTo, $limit = 100000, $flag = 0): array
    {
        $baseQuery = $this->getFullUrlWithKey('/v1/supplier/reportDetailByPeriod') .
            "&flag=$flag&dateFrom=$rfcDateFrom&dateTo=$rfcDateTo";
        $reports = [];
        $rrdid = 0;

        do {
            $query = $baseQuery . "&limit=$limit&rrdid=$rrdid";

            $response = Http::get($query);
            $newReports = $response->json();

            if ($response->status() !== 200) {
                throw new Exception(
                    "Fetch reports error. Date from: $rfcDateFrom. " .
                    'Response status: ' . $response->status() . '. ' .
                    "Limit: $limit. Rrdid: $rrdid"
                );
            }

            if ($newReports) {
                $reports = array_merge($reports, $newReports);
                $rrdid = $newReports[array_key_last($newReports)]['rrd_id'];
            }
        } while ($newReports);

        return $reports;
    }

    /**
     * @throws QueryException
     */
    private function insertReports($reports)
    {
        $data = [];

        foreach ($reports as $report) {
            $data[] = [
                'date_from' => $this->rfcToDate($report['date_from']),
                'date_to' => $this->rfcToDate($report['date_to']),
                'realizationreport_id' => $report['realizationreport_id'],
                'suppliercontract_code' => $report['suppliercontract_code'],
                'rrd_id' => $report['rrd_id'],
                'gi_id' => $report['gi_id'],
                'subject_name' => $report['subject_name'],
                'nm_id' => $report['nm_id'],
                'brand_name' => $report['brand_name'],
                'sa_name' => $report['sa_name'],
                'ts_name' => $report['ts_name'],
                'barcode' => $report['barcode'],
                'doc_type_name' => $report['doc_type_name'],
                'quantity' => $report['quantity'],
                'retail_price' => $report['retail_price'],
                'retail_amount' => $report['retail_amount'],
                'sale_percent' => $report['sale_percent'],
                'commission_percent' => $report['commission_percent'],
                'office_name' => $report['office_name'],
                'supplier_oper_name' => $report['supplier_oper_name'],
                'order_dt' => $this->rfcToDate($report['order_dt']),
                'sale_dt' => $this->rfcToDate($report['sale_dt']),
                'rr_dt' => $this->rfcToDate($report['rr_dt']),
                'shk_id' => $report['shk_id'],
                'retail_price_withdisc_rub' => $report['retail_price_withdisc_rub'],
                'delivery_amount' => $report['delivery_amount'],
                'return_amount' => $report['return_amount'],
                'delivery_rub' => $report['delivery_rub'],
                'gi_box_type_name' => $report['gi_box_type_name'],
                'product_discount_for_report' => $report['product_discount_for_report'],
                'supplier_promo' => $report['supplier_promo'],
                'rid' => $report['rid'],
                'ppvz_spp_prc' => $report['ppvz_spp_prc'],
                'ppvz_kvw_prc_base' => $report['ppvz_kvw_prc_base'],
                'ppvz_kvw_prc' => $report['ppvz_kvw_prc'],
                'ppvz_sales_commission' => $report['ppvz_sales_commission'],
                'ppvz_for_pay' => $report['ppvz_for_pay'],
                'ppvz_reward' => $report['ppvz_reward'],
                'ppvz_vw' => $report['ppvz_vw'],
                'ppvz_vw_nds' => $report['ppvz_vw_nds'],
                'ppvz_office_id' => $report['ppvz_office_id'],
                'ppvz_supplier_id' => $report['ppvz_supplier_id'],
                'ppvz_supplier_name' => $report['ppvz_supplier_name'],
                'ppvz_inn' => $report['ppvz_inn'],
                'declaration_number' => $report['declaration_number'],
                'sticker_id' => $report['sticker_id'],
                'site_country' => $report['site_country'],
                'penalty' => $report['penalty'],
                'additional_payment' => $report['additional_payment'],
                'srid' => $report['srid'],
            ];
        }

        Report::insert($data);
    }

    /**
     * @throws QueryException
     */
    private function getExciseGoodsDateFrom(): ?Carbon
    {
        $lastChangeDate = ExciseGood::max('date');
        return is_null($lastChangeDate) ? null : new Carbon($lastChangeDate);
    }

    /**
     * @throws Exception
     */
    private function fetchExciseGoods($rfcDateFrom): array
    {
        $response = Http::get(
            $this->getFullUrlWithKey('/v1/supplier/excise-goods') .
            "&dateFrom=$rfcDateFrom"
        );

        if ($response->status() !== 200) {
            throw new Exception("Fetch excise goods error. Date from: $rfcDateFrom. Response status: " . $response->status());
        }

        return $response->json();
    }

    /**
     * @throws QueryException
     */
    private function insertExciseGoods($exciseGoods)
    {
        $data = [];

        foreach ($exciseGoods as $excise) {
            $data[] = [
                'wb_id' => $excise['id'],
                'inn' => $excise['inn'],
                'finishedPrice' => $excise['finishedPrice'],
                'operationTypeId' => $excise['operationTypeId'],
                'fiscalDt' => $excise['fiscalDt'],
                'docNumber' => $excise['docNumber'],
                'fnNumber' => $excise['fnNumber'],
                'regNumber' => $excise['regNumber'],
                'excise' => $excise['excise'],
                'date' => $excise['date'],
            ];
        }

        ExciseGood::insert($data);
    }

    private function getFullUrlWithKey($path): string
    {
        return $this->apiUrl . $path . '?key=' . $this->apiKey;
    }

    private function dateTimeToRfc(Carbon $date): string
    {
        return $date->format(self::RFC_TIME_FORMAT);
    }

    private function dateTimeOrDefaultToRfc(?Carbon $date): string
    {
        return is_null($date) ?
            $this->dateTimeToRfc($this->defaultDateFrom) :
            $this->dateTimeToRfc($date);
    }

    private function rfcToDate($rfcDate): Carbon|bool
    {
        return Carbon::createFromFormat(DateTimeInterface::RFC3339, $rfcDate);
    }

    private function logQueryError($e)
    {
        $this->log->error('Query error: ' . $e->getMessage() . 'Line: ' . $e->getLine());
    }
}
