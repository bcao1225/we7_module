<?php
 goto bH0ZJ; tvtet: $data["\x61\x64\x31\137\x75\x72\x6c"] = $_GPC["\141\x64\x31\137\165\162\x6c"]; goto xDqI_; ypT80: $data["\141\x64\137\x68\x65\x69\147\x68\164"] = $_GPC["\141\144\137\x68\x65\x69\147\x68\164"]; goto DtT7A; HgRbz: message("\xe4\xbf\xae\xe6\x94\xb9\346\x88\x90\345\x8a\237"); goto ykjNo; DdBUn: $data = pdo_fetch("\123\x45\x4c\x45\x43\x54\x20\x2a\x20\x46\x52\x4f\x4d\x20" . tablename("\x79\171\146\137\143\157\x6d\160\141\156\171\137\x61\x64\x73\x65\x6e\x73\145") . "\x20\167\x68\145\x72\145\x20\140\x75\x6e\151\141\143\151\144\140\75\x27{$uniacid}\x27\40\157\162\144\x65\x72\40\x62\171\x20\151\144\x20\144\145\x73\x63\x20\154\x69\x6d\151\x74\x20\x31"); goto IQDEl; xDqI_: $data["\141\144\62\x5f\165\x72\154"] = $_GPC["\x61\144\x32\x5f\x75\x72\x6c"]; goto GE9tK; mfLtK: $data["\141\144\x31\137\x69\155\x67"] = $_GPC["\x61\144\x31\x5f\151\x6d\x67"]; goto l2Gh1; ivWC5: $result = pdo_fetch("\123\x45\114\x45\x43\x54\x20\x60\x69\144\x60\x20\106\122\x4f\x4d\40" . tablename("\x79\171\x66\x5f\143\157\155\x70\x61\x6e\x79\x5f\x61\x64\163\x65\x6e\163\x65") . "\x20\x77\150\x65\162\145\40\140\x75\x6e\x69\x61\143\x69\x64\140\x3d\47{$uniacid}\x27\x20\157\x72\x64\x65\x72\x20\x62\171\x20\151\x64\x20\144\x65\163\x63\40\154\151\155\x69\x74\x20\61"); goto D2NBa; IQDEl: $NDSd1 = !checksubmit(); goto DViDx; DU4Sp: load()->func("\164\160\154"); goto DdBUn; v_xTZ: bXlT_: goto Suns0; DViDx: if ($NDSd1) { goto VM9KS; } goto Wow_X; BAu68: rfF9p: goto IyRax; DtT7A: $data["\x61\x64\61\x5f\150\x65\151\x67\x68\x74"] = $_GPC["\141\144\x31\x5f\x68\x65\x69\147\x68\x74"]; goto cEGek; SsAsH: VM9KS: goto osxu2; DI8uZ: goto bXlT_; goto BAu68; D2NBa: if ($result["\151\144"]) { goto rfF9p; } goto ayXYs; ayXYs: $res = pdo_insert("\171\171\146\x5f\143\157\x6d\x70\x61\156\171\x5f\141\144\163\145\156\x73\145", $data); goto DI8uZ; l2Gh1: $data["\141\144\62\137\151\x6d\147"] = $_GPC["\x61\144\62\137\151\x6d\x67"]; goto ivWC5; NYLS1: if ($UBfvy) { goto mci7O; } goto HgRbz; cEGek: $data["\x61\x64\x5f\x75\162\x6c"] = $_GPC["\x61\144\x5f\165\162\x6c"]; goto tvtet; ykjNo: mci7O: goto SsAsH; GE9tK: $data["\x61\144\137\x69\x6d\x67"] = $_GPC["\x61\144\x5f\x69\x6d\147"]; goto mfLtK; osxu2: include $this->template("\141\x64\163\x65\x6e\x73\x65"); goto Ehq32; UV03G: $uniacid = $_W["\x75\x6e\151\x61\x63\151\144"]; goto DU4Sp; bH0ZJ: global $_W, $_GPC; goto UV03G; Suns0: $UBfvy = !$res; goto NYLS1; IyRax: $res = pdo_update("\171\x79\146\x5f\143\x6f\155\160\x61\156\x79\137\141\144\x73\145\x6e\x73\x65", $data, array("\151\x64" => $result["\151\x64"])); goto v_xTZ; zPI6E: $data["\165\156\151\141\x63\151\x64"] = $_W["\165\156\151\141\x63\151\x64"]; goto ypT80; Wow_X: $data = array(); goto zPI6E; Ehq32: ?>