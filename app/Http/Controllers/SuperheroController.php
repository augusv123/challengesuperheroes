<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Superhero;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Response;

class SuperheroController extends Controller
{


    public function uploadContent(Request $request)
    {
        $file = $request->file('uploaded_file');
        if ($file) {
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();

            $this->checkUploadedFileProperties($extension, $fileSize);

            $location = 'uploads';

            $file->move($location, $filename);

            $filepath = public_path($location . "/" . $filename);

            $file = fopen($filepath, "r");
            $importData_arr = array();
            $i = 0;

            while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                $num = count($filedata);

                if ($i == 0) {
                    $i++;
                    continue;
                }
                for ($c = 0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata[$c];
                }
                $i++;
            }
            fclose($file);
            $j = 0;
            foreach ($importData_arr as $importData) {

                $j++;
                try {

                    DB::beginTransaction();
                    Superhero::create([
                        'name' => $importData[1],
                        'fullName' => $importData[2],
                        'strength' => $importData[3],
                        'speed' => $importData[4],
                        'durability' => $importData[5],
                        'power' => $importData[6],
                        'combat' => $importData[7],
                        'race' => $importData[8],
                        'height/0' => $importData[9],
                        'height/1' => $importData[10],
                        'weight/0' => $importData[11],
                        'weight/1' => $importData[12],
                        'eyeColor' => $importData[13],
                        'hairColor' => $importData[14],
                        'publisher' => $importData[15]
                    ]);

                    DB::commit();
                } catch (\Exception $e) {

                    return json_encode($e);

                    DB::rollBack();
                }
            }
            return response()->json([
                'message' => "$j superheroes agregados"
            ]);
        } else {
            //no file was uploaded
            throw new \Exception('No se subio ningun archivo', Response::HTTP_BAD_REQUEST);
        }
    }
    public function checkUploadedFileProperties($extension, $fileSize)
    {
        $valid_extension = array("csv", "xlsx");
        $maxFileSize = 2297152;
        if (in_array(strtolower($extension), $valid_extension)) {
            if ($fileSize <= $maxFileSize) {
            } else {
                throw new \Exception('No file was uploaded', Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
            }
        } else {
            throw new \Exception('Invalid file extension', Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }
    }

    public function getSuperheroes(Request $request, Superhero $superhero)
    {
        $superhero = $superhero->newQuery();

        if ($request->has('name')) {
            $superhero->where('name', $request->input('name'));
        }



        if ($request->has('race')) {
            $superhero->where('city', $request->input('city'));
        }

        if ($request->has('publisher')) {
            $superhero->where('city', $request->input('city'));
        }


        return $superhero->orderBy('strength', 'ASC')->paginate(20);
    }
}
