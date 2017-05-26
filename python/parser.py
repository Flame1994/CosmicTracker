class Parser:

    @staticmethod
    def parse(anomaly):
        items = anomaly.strip().split('\t')
        data = {"id": items[0], "type": items[1], "name": items[2]}
        return data


def main():
    print(Parser.parse("GJM-840 Relic Site30%  4,94 AU"))

if __name__ == "__main__":
    main()